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
     * The default input name and POST parameter used by Google reCAPTCHA.
     *
     * @const string
     */
    const DEFAULT_FIELD_NAME = 'g-recaptcha-response';

    /**
     * Form control name and POST parameter when the user submits the form on your site.
     *
     * @var string
     */
    private $field;

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
            'field'       => static::DEFAULT_FIELD_NAME,
            'public_key'  => '',
            'private_key' => ''
        ];
    }

    /**
     * Define the field name to validate the reCAPTCHA response.
     *
     * @param  string $name The input name used as the POST parameter.
     * @throws InvalidArgumentException If the field name is not a string.
     * @return self
     */
    public function setField($name)
    {
        if (!is_string($name)) {
            throw new InvalidArgumentException(sprintf(
                'The field name must be a string, received %s',
                is_object($name) ? get_class($name) : gettype($name)
            ));
        }

        $this->field = $name;

        return $this;
    }

    /**
     * Retrieve the field name containing the reCAPTCHA response.
     *
     * @return string
     */
    public function field()
    {
        return $this->field;
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
