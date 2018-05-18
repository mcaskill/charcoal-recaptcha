<?php

namespace Charcoal\ReCaptcha;

use RuntimeException;
use InvalidArgumentException;

// From PSR-7
use Psr\Http\Message\ServerRequestInterface as HttpRequest;
use Psr\Http\Message\ResponseInterface as HttpResponse;

// From Google
use ReCaptcha\ReCaptcha;
use ReCaptcha\RequestMethod as ApiRequest;
use ReCaptcha\Response as ApiResponse;

// From 'charcoal-config'
use Charcoal\Config\ConfigurableTrait;

// From 'charcoal-recaptcha'
use Charcoal\ReCaptcha\CaptchaConfig;

/**
 * Service wrapper for the Google reCAPTCHA client
 *
 * This class can be used as a PSR-7 middleware or as an object in your service layer.
 */
class Captcha
{
    use ConfigurableTrait {
        ConfigurableTrait::setConfig as private;
    }

    /**
     * The JavaScript API for Google reCAPTCHA.
     *
     * @const string
     */
    const CLIENT_API = 'https://www.google.com/recaptcha/api.js';

    /**
     * Store the ReCaptcha client.
     *
     * @var ReCaptcha
     */
    private $client;

    /**
     * Store the last ReCaptcha response.
     *
     * @var ApiResponse
     */
    private $lastResponse;

    /**
     * @param  array $data The constructor options.
     * @return void
     */
    public function __construct(array $data)
    {
        $this->setConfig($data['config']);
        $this->setClient($data['client']);
    }

    /**
     * Execute Middleware
     *
     * @param  HttpRequest  $request  A PSR-7 compatible Request instance.
     * @param  HttpResponse $response A PSR-7 compatible Response instance.
     * @param  callable     $next     Next callable middleware.
     * @throws InvalidArgumentException If the CAPTCHA is invalid.
     * @return HttpResponse
     */
    public function __invoke(
        HttpRequest $request,
        HttpResponse $response,
        callable $next
    ) {
        $valid = $this->verifyRequest($request);

        if ($valid) {
            return $next($request, $response);
        } else {
            $messages = $this->getLastErrorMessages();
            throw new InvalidArgumentException(array_pop($messages));
        }
    }

    /**
     * Create a new CaptchaConfig instance.
     *
     * @param  array|null $data Optional data to pass to the new ConfigInterface instance.
     * @return CaptchaConfig
     */
    protected function createConfig($data = null)
    {
        return new CaptchaConfig($data);
    }

    /**
     * Retrieve the ReCaptcha client.
     *
     * @return ReCaptcha
     */
    public function client()
    {
        return $this->client;
    }

    /**
     * Set the reCAPTCHA client.
     *
     * @param  ReCaptcha $client The CAPTCHA service.
     * @return void
     */
    private function setClient(ReCaptcha $client)
    {
        $this->client = $client;
    }

    /**
     * Call the reCAPTCHA API to verify whether the user passes CAPTCHA test.
     *
     * @param  mixed  $input    The value of 'g-recaptcha-response' in the submitted form.
     * @param  string $remoteIp The end user's IP address.
     * @return boolean Returns TRUE if 'g-recaptcha-response' is valid, FALSE otherwise.
     */
    public function verify($input, $remoteIp)
    {
        $this->lastResponse = $this->client()->verify($input, $remoteIp);

        return $this->lastResponse->isSuccess();
    }

    /**
     * Verify no-captcha response by Symfony Request.
     *
     * @param  HttpRequest $request A PSR-7 compatible Request instance.
     * @return boolean Returns TRUE if 'g-recaptcha-response' is valid, FALSE otherwise.
     */
    public function verifyRequest(HttpRequest $request)
    {
        $token    = $this->extractTokenFromRequest($request);
        $remoteIp = $this->extractRemoteIpFromRequest($request);

        return $this->verify($token, $remoteIp);
    }

    /**
     * Extract the user response token, provided by Google reCAPTCHA, from the server request.
     *
     * @param  HttpRequest $request A PSR-7 compatible Request instance.
     * @return string|null Returns the user response token or NULL.
     */
    private function extractTokenFromRequest(HttpRequest $request)
    {
        $key = $this->config('input_key');

        if (is_callable([ $request, 'getParam' ])) {
            return $request->getParam($key);
        }

        $postParams = $request->getParsedBody();
        if (is_array($postParams) && isset($postParams[$key])) {
            return $postParams[$key];
        } elseif (is_object($postParams) && property_exists($postParams, $key)) {
            return $postParams->$key;
        }

        $getParams = $request->getQueryParams();
        if (isset($getParams[$key])) {
            return $getParams[$key];
        }

        return null;
    }

    /**
     * Extract the remote IP address from the server request.
     *
     * @param  HttpRequest $request A PSR-7 compatible Request instance.
     * @return string|null Returns the remote IP address or NULL.
     */
    private function extractRemoteIpFromRequest(HttpRequest $request)
    {
        if (is_callable([ $request, 'getServerParam' ])) {
            return $request->getServerParam('REMOTE_ADDR');
        }

        $serverParams = $request->getServerParams();
        return isset($serverParams['REMOTE_ADDR']) ? $serverParams['REMOTE_ADDR'] : null;
    }

    /**
     * Retrieve the response object (if any) response object from the last CAPTCHA test.
     *
     * @throws RuntimeException If the CAPTCHA was not tested.
     * @return ApiResponse
     */
    public function getLastResponse()
    {
        if ($this->lastResponse === null) {
            throw new RuntimeException(sprintf(
                'CAPTCHA Untested'
            ));
        }

        return $this->lastResponse;
    }

    /**
     * Retrieve the error codes from the last CAPTCHA test (if any).
     *
     * @return array
     */
    public function getLastErrorCodes()
    {
        return $this->getLastResponse()->getErrorCodes();
    }

    /**
     * Retrieve the error messages from the last CAPTCHA test (if any).
     *
     * @return array
     */
    public function getLastErrorMessages()
    {
        $codes = $this->getLastErrorCodes();

        return $this->getErrorMessages($codes);
    }

    /**
     * Retrieve the messages for the given error codes.
     *
     * @param  array $codes The error codes to resolve.
     * @return array
     */
    protected function getErrorMessages(array $codes)
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
     * @link   https://developers.google.com/recaptcha/docs/verify
     * @param  string $code An error code to resolve.
     * @return array
     */
    protected function getErrorMessage($code)
    {
        switch ($code) {
            case 'missing-input-secret':
                return 'The reCAPTCHA secret parameter is missing.';

            case 'invalid-input-secret':
                return 'The reCAPTCHA secret parameter is invalid or malformed.';

            case 'missing-input':
            case 'missing-input-response':
                return 'The CAPTCHA response parameter is missing.';

            case 'invalid-input':
            case 'invalid-input-response':
                return 'The CAPTCHA response parameter is invalid or malformed.';

            default:
                return strtr('Unknown reCAPTCHA error: {code}', [
                    '{code}' => $code
                ]);
        }
    }

    /**
     * Render the HTML script and widget.
     *
     * @param  array|boolean|null $attributes  The HTML attributes for the 'g-recaptcha' tag.
     * @param  array|boolean|null $queryParams The query parameters for the JavaScript API link.
     * @return string
     */
    public function display($attributes = true, $queryParams = true)
    {
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
     * @param  array|null $attributes The HTML attributes for the 'g-recaptcha' tag.
     * @return string
     */
    public function getWidgetHtml(array $attributes = [])
    {
        $attributes['data-sitekey'] = $this->config('public_key');

        return '<div class="g-recaptcha"' . $this->buildAttributes($attributes) . '></div>';
    }

    /**
     * Build HTML attributes.
     *
     * @param  array $attributes Associative array of attribute names and values.
     * @return string Returns a string of HTML attributes.
     */
    protected function buildAttributes(array $attributes)
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
     * @param  array|null $query Array of query string arguments to customize the API.
     * @return string Returns an HTML `<script>` element.
     */
    public function getJsHtml(array $query = null)
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
     * @param  array $query Array of query string arguments to customize the API.
     * @return string Returns a URI.
     */
    public function getJsUri(array $query = null)
    {
        if (isset($query['lang'])) {
            $query['hl'] = $query['lang'];
            unset($query['lang']);
        }

        if ($query) {
            return static::CLIENT_API . '?' . http_build_query($query);
        } else {
            return static::CLIENT_API;
        }
    }
}
