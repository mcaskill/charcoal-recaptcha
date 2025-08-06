<?php

namespace Charcoal\ReCaptcha;

use Charcoal\ReCaptcha\CaptchaConfig;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface as HttpResponse;
use Psr\Http\Message\ServerRequestInterface as HttpRequest;
use ReCaptcha\ReCaptcha;
use ReCaptcha\Response as ApiResponse;
use RuntimeException;

/**
 * Service wrapper for the Google reCAPTCHA client
 *
 * This class can be used as a PSR-7 middleware or as an object in your service layer.
 */
class Captcha
{
    /**
     * The JavaScript API for Google reCAPTCHA.
     *
     * @var string
     */
    public const CLIENT_API = 'https://www.google.com/recaptcha/api.js';

    /**
     * Store the last ReCaptcha response.
     */
    private ?ApiResponse $lastResponse = null;

    public function __construct(
        private CaptchaConfig $config,
        private ?ReCaptcha $client = null
    ) {}

    /**
     * Execute Middleware
     *
     * @param  (callable(HttpRequest, HttpResponse): HttpResponse) $next Next middleware.
     * @throws InvalidArgumentException If the CAPTCHA is invalid.
     */
    public function __invoke(
        HttpRequest $request,
        HttpResponse $response,
        callable $next
    ): HttpResponse {
        if ($this->verifyRequest($request)) {
            return $next($request, $response);
        }

        throw new InvalidArgumentException(array_pop($this->getLastErrorMessages()));
    }

    public function getConfig(): CaptchaConfig
    {
        return $this->config;
    }

    /**
     * Retrieve the ReCaptcha client.
     *
     * @return ReCaptcha
     */
    public function getClient(): ReCaptcha
    {
        return $this->client ??= new ReCaptcha($this->getConfig()->getPrivateKey());
    }

    /**
     * Call the reCAPTCHA API to verify whether the user passes CAPTCHA test.
     *
     * @param  string $input    The value of 'g-recaptcha-response' in the submitted form.
     * @param  string $remoteIp The end user's IP address.
     * @return bool Returns TRUE if 'g-recaptcha-response' is valid, FALSE otherwise.
     */
    public function verify(string $input, string $remoteIp): bool
    {
        $this->lastResponse = $this->getClient()->verify($input, $remoteIp);

        return $this->lastResponse->isSuccess();
    }

    /**
     * Verify no-captcha response by Symfony Request.
     *
     * @param  HttpRequest $request A PSR-7 compatible Request instance.
     * @return bool Returns TRUE if 'g-recaptcha-response' is valid, FALSE otherwise.
     */
    public function verifyRequest(HttpRequest $request): bool
    {
        $token    = $this->extractTokenFromRequest($request);
        $remoteIp = $this->extractRemoteIpFromRequest($request);

        return $this->verify($token, $remoteIp);
    }

    /**
     * Extract the user response token, provided by Google reCAPTCHA, from the server request.
     *
     * @param  HttpRequest $request A PSR-7 compatible Request instance.
     * @return ?string Returns the user response token or NULL.
     */
    private function extractTokenFromRequest(HttpRequest $request): ?string
    {
        $key = $this->getConfig()->getInputKey();

        $postParams = $request->getParsedBody();
        return $postParams[$key]
            ?? $postParams->$key
            ?? $request->getQueryParams()[$key]
            ?? null;
    }

    /**
     * Extract the remote IP address from the server request.
     *
     * @param  HttpRequest $request A PSR-7 compatible Request instance.
     * @return ?string Returns the remote IP address or NULL.
     */
    private function extractRemoteIpFromRequest(HttpRequest $request): ?string
    {
        return $request->getServerParams()['REMOTE_ADDR'] ?? null;
    }

    /**
     * Retrieve the response object (if any) response object from the last CAPTCHA test.
     *
     * @throws RuntimeException If the CAPTCHA was not tested.
     */
    public function getLastResponse(): ApiResponse
    {
        return $this->lastResponse ?? throw new RuntimeException('CAPTCHA Untested');
    }

    /**
     * Retrieve the error codes from the last CAPTCHA test (if any).
     *
     * @return string[]
     */
    public function getLastErrorCodes(): array
    {
        return $this->getLastResponse()->getErrorCodes();
    }

    /**
     * Retrieve the error messages from the last CAPTCHA test (if any).
     *
     * @return array<string, mixed>
     */
    public function getLastErrorMessages(): array
    {
        $codes = $this->getLastErrorCodes();

        return $this->getErrorMessages($codes);
    }

    /**
     * Retrieve the messages for the given error codes.
     *
     * @param  string[] $codes The error codes to resolve.
     * @return array<string, mixed>
     */
    public function getErrorMessages(array $codes): array
    {
        $messages = [];
        foreach ($codes as $code) {
            $messages[$code] = $this->getErrorMessage($code);
        }

        return $messages;
    }

    /**
     * Retrieve the message for the given error code.
     *
     * @link  https://developers.google.com/recaptcha/docs/verify
     * @param string $code An error code to resolve.
     */
    public function getErrorMessage(string $code): string
    {
        return match ($code) {
            'missing-input-secret' => 'The reCAPTCHA secret parameter is missing.',

            'invalid-input-secret' => 'The reCAPTCHA secret parameter is invalid or malformed.',

            'missing-input',
            'missing-input-response' => 'The CAPTCHA response parameter is missing.',

            'invalid-input',
            'invalid-input-response' => 'The CAPTCHA response parameter is invalid or malformed.',

            default => strtr('Unknown reCAPTCHA error: {code}', [ '{code}' => $code ]),
        };
    }

    /**
     * Render the HTML script and widget.
     *
     * @param  array<string, mixed>|bool|null $attributes  The HTML attributes for the 'g-recaptcha' tag.
     * @param  array<string, mixed>|bool|null $queryParams The query parameters for the JavaScript API link.
     */
    public function display(
        array|bool|null $attributes = true,
        array|bool|null $queryParams = true
    ): string {
        $html = '';

        if ($queryParams) {
            if (!is_array($queryParams)) {
                $queryParams = [];
            }

            $html .= $this->getJsHtml($queryParams);
        }

        if ($attributes) {
            if (!is_array($attributes)) {
                $attributes = [];
            }

            if ($html) {
                $html .= "\n";
            }

            $html .= $this->getWidgetHtml($attributes);
        }

        return $html;
    }

    /**
     * Render the HTML widget.
     *
     * @link   https://developers.google.com/recaptcha/docs/display
     * @param  array<string, mixed> $attributes The HTML attributes for the 'g-recaptcha' tag.
     * @return string
     */
    public function getWidgetHtml(array $attributes = []): string
    {
        $attributes['data-sitekey'] = $this->getConfig()->getPublicKey();

        return '<div class="g-recaptcha"' . $this->buildAttributes($attributes) . '></div>';
    }

    /**
     * Build HTML attributes.
     *
     * @param  array<string, mixed> $attributes Associative array of attribute names and values.
     * @return string Returns a string of HTML attributes.
     */
    protected function buildAttributes(array $attributes): string
    {
        $html = [];
        foreach ($attributes as $key => $value) {
            $html[] = $key . '="' . $value . '"';
        }

        return $html ? ' ' . implode(' ', $html) : '';
    }

    /**
     * Create the HTML `<script>` element to load the JavaScript API.
     *
     * @param  ?array<string, mixed> $query Array of query string arguments to customize the API.
     * @return string Returns an HTML `<script>` element.
     */
    public function getJsHtml(?array $query = null): string
    {
        return sprintf(
            '<script src="%s" async defer></script>',
            $this->getJsUri($query)
        );
    }

    /**
     * Create the URI to the JavaScript API with parameters.
     *
     * @link   https://developers.google.com/recaptcha/docs/display
     * @param  ?array<string, mixed> $query Array of query string arguments to customize the API.
     * @return string Returns a URI.
     */
    public function getJsUri(?array $query = null): string
    {
        if (isset($query['lang'])) {
            $query['hl'] = $query['lang'];
            unset($query['lang']);
        }

        if ($query) {
            return static::CLIENT_API . '?' . http_build_query($query);
        }

        return static::CLIENT_API;
    }
}
