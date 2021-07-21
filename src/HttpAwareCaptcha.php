<?php

declare(strict_types=1);

namespace Charcoal\ReCaptcha;

use Charcoal\ReCaptcha\Exceptions\InvalidArgumentException;
use Psr\Http\Message\ServerRequestInterface as HttpRequest;
use Psr\Http\Message\ResponseInterface as HttpResponse;

use function array_pop;
use function is_array;
use function is_callable;
use function is_object;
use function property_exists;

/**
 * PSR-7 aware service wrapper for the Google reCAPTCHA client
 */
class HttpAwareCaptcha extends AbstractCaptchaDecorator
{
    /**
     * Execute middleware.
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
    ): HttpResponse {
        $valid = $this->verifyRequest($request);

        if ($valid) {
            return $next($request, $response);
        }

        $messages = $this->getLastErrorMessages();
        throw new InvalidArgumentException(array_pop($messages), 400);
    }

    /**
     * Verify reCAPTCHA response from a HTTP request.
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
     * Extract the remote IP address from the server request.
     *
     * @param  HttpRequest $request A PSR-7 compatible Request instance.
     * @return ?string Returns the remote IP address or NULL.
     */
    private function extractRemoteIpFromRequest(HttpRequest $request): ?string
    {
        if (is_callable([ $request, 'getServerParam' ])) {
            return $request->getServerParam('REMOTE_ADDR');
        }

        $serverParams = $request->getServerParams();
        return ($serverParams['REMOTE_ADDR'] ?? null);
    }

    /**
     * Extract the user response token, provided by Google reCAPTCHA, from the server request.
     *
     * @param  HttpRequest $request A PSR-7 compatible Request instance.
     * @return ?string Returns the user response token or NULL.
     */
    private function extractTokenFromRequest(HttpRequest $request): ?string
    {
        $key = $this->getConfig('input_key');

        if (is_callable([ $request, 'getParam' ])) {
            return $request->getParam($key);
        }

        $postParams = $request->getParsedBody();
        if (is_array($postParams) && isset($postParams[$key])) {
            return $postParams[$key];
        } elseif (is_object($postParams) && property_exists($postParams, $key)) {
            return $postParams->{$key};
        }

        if (is_callable([ $request, 'getQueryParam' ])) {
            return $request->getQueryParam($key);
        }

        $getParams = $request->getQueryParams();
        if (isset($getParams[$key])) {
            return $getParams[$key];
        }

        return null;
    }
}
