<?php

namespace Charcoal\ReCaptcha;

use InvalidArgumentException;

// From 'charcoal-config'
use Charcoal\Config\AbstractConfig;

/**
 * Google reCAPTCHA Configuration
 */
class CaptchaConfig extends AbstractConfig
{
    /**
     * The default input name and GET / POST parameter key used by Google reCAPTCHA.
     *
     * @const string
     */
    const DEFAULT_INPUT_PARAM_KEY = 'g-recaptcha-response';

    /**
     * Form control name and POST parameter when the user submits the form on your site.
     *
     * @var string
     */
    private $inputKey;

    /**
     * The site key used for displaying the reCAPTCHA widget.
     *
     * @var string
     */
    private $publicKey;

    /**
     * The secret key shared between your site and reCAPTCHA.
     *
     * @var string
     */
    private $privateKey;

    /**
     * Retrieve the default reCAPTCHA service settings.
     *
     * @return array
     */
    public function defaults()
    {
        return [
            'input_key'   => static::DEFAULT_INPUT_PARAM_KEY,
            'public_key'  => '',
            'private_key' => '',
        ];
    }

    /**
     * Retrieve the HTTP parameter key of the user response token to validate.
     *
     * @return string
     */
    public function inputKey()
    {
        return $this->inputKey;
    }

    /**
     * Set the HTTP parameter key of the user response token to validate.
     *
     * @param  string $key The parameter key on an HTTP request to lookup.
     * @throws InvalidArgumentException If the parameter key is not a string.
     * @return self
     */
    public function setInputKey($key)
    {
        if (!is_string($key)) {
            throw new InvalidArgumentException(sprintf(
                'The parameter key must be a string, received %s',
                is_object($key) ? get_class($key) : gettype($key)
            ));
        }

        $this->inputKey = $key;
        return $this;
    }

    /**
     * Retrieve the public key used for displaying the reCAPTCHA widget.
     *
     * @return string
     */
    public function publicKey()
    {
        return $this->publicKey;
    }

    /**
     * Set the public key used for displaying the reCAPTCHA widget.
     *
     * @param  string $key The public key.
     * @throws InvalidArgumentException If the public key is not a string.
     * @return self
     */
    public function setPublicKey($key)
    {
        if (!is_string($key)) {
            throw new InvalidArgumentException(sprintf(
                'The public key must be a string, received %s',
                is_object($key) ? get_class($key) : gettype($key)
            ));
        }

        $this->publicKey = $key;
        return $this;
    }

    /**
     * Retrieve the private key shared between your site and reCAPTCHA.
     *
     * @return string
     */
    public function privateKey()
    {
        return $this->privateKey;
    }

    /**
     * Set the private key shared between your site and reCAPTCHA.
     *
     * @param  string $key The private key.
     * @throws InvalidArgumentException If the private key is not a string.
     * @return self
     */
    public function setPrivateKey($key)
    {
        if (!is_string($key)) {
            throw new InvalidArgumentException(sprintf(
                'The private key must be a string, received %s',
                is_object($key) ? get_class($key) : gettype($key)
            ));
        }

        $this->privateKey = $key;
        return $this;
    }
}
