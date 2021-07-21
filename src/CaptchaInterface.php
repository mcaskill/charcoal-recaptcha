<?php

declare(strict_types=1);

namespace Charcoal\ReCaptcha;

use Charcoal\ReCaptcha\CaptchaConfig as Config;
use ReCaptcha\ReCaptcha;
use ReCaptcha\Response as ApiResponse;

/**
 * Service wrapper for the Google reCAPTCHA client
 *
 * This class can be used as a PSR-7 middleware or as an object in your service layer.
 */
interface CaptchaInterface
{
    /**
     * Call the reCAPTCHA API to verify whether the user passes CAPTCHA test.
     *
     * @param  string      $input    The value of 'g-recaptcha-response' in the submitted form.
     * @param  string|null $remoteIp Optional. The end user's IP address.
     * @return bool Returns TRUE if 'g-recaptcha-response' is valid, FALSE otherwise.
     */
    public function verify(string $input, ?string $remoteIp = null): bool;

    /**
     * Retrieve the ReCaptcha client.
     *
     * @return ReCaptcha
     */
    public function getClient(): ReCaptcha;

    /**
     * Retrieve the CAPTCHA configset or a specific key from the container.
     *
     * @param  string|null $key     Optional. If provided, the data key to retrieve.
     * @param  mixed       $default Optional. The fallback value to return if $key does not exist.
     * @return mixed If $key is NULL, the {@see Config} object is returned.
     *     If $key is given, its value on the {@see Config} object is returned.
     *     If the value of $key is NULL, the value of $default is returned.
     */
    public function getConfig(string $key = null, $default = null);

    /**
     * Retrieve the ReCaptcha response from the last CAPTCHA verification.
     *
     * @return ?ApiResponse
     */
    public function getLastResponse(): ?ApiResponse;

    /**
     * Retrieve the error codes from the last CAPTCHA verification.
     *
     * @return ?(string|int)[]
     */
    public function getLastErrorCodes(): ?iterable;

    /**
     * Retrieve the error messages from the last CAPTCHA verification.
     *
     * @return ?string[]
     */
    public function getLastErrorMessages(): ?iterable;

    /**
     * Retrieve the messages for the given error codes.
     *
     * @param  (string|int)[] $codes The error codes to resolve.
     * @return string[]
     */
    public function getErrorMessages(iterable $codes): iterable;

    /**
     * Retrieve the message for the given error code.
     *
     * @link   https://developers.google.com/recaptcha/docs/verify
     * @param  string|int $code An error code to resolve.
     * @return string
     */
    public function getErrorMessage($code): string;
}
