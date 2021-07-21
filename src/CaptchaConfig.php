<?php

declare(strict_types=1);

namespace Charcoal\ReCaptcha;

use Charcoal\Config\AbstractConfig;

/**
 * Service settings for the Google reCAPTCHA client
 */
class CaptchaConfig extends AbstractConfig
{
    /**
     * The default input name and GET / POST parameter key used by Google reCAPTCHA.
     *
     * @const string
     */
    public const DEFAULT_INPUT_PARAM_KEY = 'g-recaptcha-response';

    /**
     * Form control name and POST parameter when the user submits the form on your site.
     *
     * @var string
     */
    private $inputKey = self::DEFAULT_INPUT_PARAM_KEY;

    /**
     * The site key used for displaying the reCAPTCHA widget.
     *
     * @var string
     */
    private $publicKey = '';

    /**
     * The secret key shared between your site and reCAPTCHA.
     *
     * @var string
     */
    private $privateKey = '';

    /**
     * The action to match against during verification.
     *
     * This should be set per page.
     *
     * @var string|null
     */
    private $action;

    /**
     * The hostname to match against during verification.
     *
     * This should be without a protocol or trailing slash, e.g. `www.google.com`.
     *
     * @var string|null
     */
    private $hostname;

    /**
     * The APK package name to match against during verification.
     *
     * @var string|null
     */
    private $apkPackageName;

    /**
     * The threshold to meet or exceed during verification.
     *
     * Threshold should be a float between 0 and 1 which will be tested as response >= threshold.
     *
     * @var float|null
     */
    private $scoreThreshold;

    /**
     * The timeout in seconds to test against the challenge timestamp during verification.
     *
     * @var int|null
     */
    private $challengeTimeout;

    /**
     * Retrieve the HTTP parameter key of the user response token to validate.
     *
     * @return string
     */
    public function getInputKey(): string
    {
        return $this->inputKey;
    }

    /**
     * Set the HTTP parameter key of the user response token to validate.
     *
     * @param  string $key The parameter key on an HTTP request to lookup.
     * @return void
     */
    public function setInputKey(string $key): void
    {
        $this->inputKey = $key;
    }

    /**
     * Retrieve the public key used for displaying the reCAPTCHA widget.
     *
     * @return string
     */
    public function getPublicKey(): string
    {
        return $this->publicKey;
    }

    /**
     * Set the public key used for displaying the reCAPTCHA widget.
     *
     * @param  string $key The public key.
     * @return void
     */
    public function setPublicKey(string $key): void
    {
        $this->publicKey = $key;
    }

    /**
     * Retrieve the private key shared between your site and reCAPTCHA.
     *
     * @return string
     */
    public function getPrivateKey(): string
    {
        return $this->privateKey;
    }

    /**
     * Set the private key shared between your site and reCAPTCHA.
     *
     * @param  string $key The private key.
     * @return void
     */
    public function setPrivateKey(string $key): void
    {
        $this->privateKey = $key;
    }

    /**
     * Retrieve the action to match against during verification.
     *
     * @return string
     */
    public function getAction(): string
    {
        return $this->action;
    }

    /**
     * Set the action to match against during verification.
     *
     * @param  string $action The action.
     * @return void
     */
    public function setAction(string $action): void
    {
        $this->action = $action;
    }

    /**
     * Retrieve the hostname to match against during verification.
     *
     * @return string
     */
    public function getHostname(): string
    {
        return $this->hostname;
    }

    /**
     * Set the hostname to match against during verification.
     *
     * @param  string $hostname The hostname.
     * @return void
     */
    public function setHostname(string $hostname): void
    {
        $this->hostname = $hostname;
    }

    /**
     * Retrieve the APK package name to match against during verification.
     *
     * @return string
     */
    public function getApkPackageName(): string
    {
        return $this->apkPackageName;
    }

    /**
     * Set the APK package name to match against during verification.
     *
     * @param  string $apkPackageName The APK package name.
     * @return void
     */
    public function setApkPackageName(string $apkPackageName): void
    {
        $this->apkPackageName = $apkPackageName;
    }

    /**
     * Retrieve the threshold to meet or exceed during verification.
     *
     * @return float
     */
    public function getScoreThreshold(): float
    {
        return $this->scoreThreshold;
    }

    /**
     * Set the threshold to meet or exceed during verification.
     *
     * @param  float $scoreThreshold The threshold.
     * @return void
     */
    public function setScoreThreshold(float $scoreThreshold): void
    {
        $this->scoreThreshold = $scoreThreshold;
    }

    /**
     * Retrieve the timeout in seconds to test against the challenge timestamp during verification.
     *
     * @return int
     */
    public function getChallengeTimeout(): int
    {
        return $this->challengeTimeout;
    }

    /**
     * Set the timeout in seconds to test against the challenge timestamp during verification.
     *
     * @param  int $timeout The timeout in seconds.
     * @return void
     */
    public function setChallengeTimeout(int $timeout): void
    {
        $this->challengeTimeout = $timeout;
    }
}
