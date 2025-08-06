<?php

namespace Charcoal\ReCaptcha;

/**
 * Google reCAPTCHA Configuration
 */
class CaptchaConfig
{
    /**
     * The default input name and GET / POST parameter key used by Google reCAPTCHA.
     *
     * @var string
     */
    public const DEFAULT_INPUT_PARAM_KEY = 'g-recaptcha-response';

    public static function createFromArray(array $data): static
    {
        return new static(
            publicKey:  ($data['public_key'] ?? $data['publicKey'] ?? ''),
            privateKey: ($data['private_key'] ?? $data['privateKey'] ?? ''),
            inputKey:   ($data['input_key'] ?? $data['inputKey'] ?? self::DEFAULT_INPUT_PARAM_KEY)
        );
    }

    /**
     * Retrieve the HTTP parameter key of the user response token to validate.
     */
    public function getInputKey(): string
    {
        return $this->inputKey;
    }

    /**
     * Set the HTTP parameter key of the user response token to validate.
     */
    public function withInputKey(string $key): static
    {
        $clone = clone $this;
        $clone->inputKey = $key;

        return $clone;
    }

    /**
     * Retrieve the public key used for displaying the reCAPTCHA widget.
     */
    public function getPublicKey(): string
    {
        return $this->publicKey;
    }

    /**
     * Set the public key used for displaying the reCAPTCHA widget.
     */
    public function withPublicKey(string $key): static
    {
        $clone = clone $this;
        $clone->publicKey = $key;

        return $clone;
    }

    /**
     * Retrieve the private key shared between your site and reCAPTCHA.
     */
    public function getPrivateKey(): string
    {
        return $this->privateKey;
    }

    /**
     * Set the private key shared between your site and reCAPTCHA.
     */
    public function withPrivateKey(string $key): static
    {
        $clone = clone $this;
        $clone->privateKey = $key;

        return $clone;
    }

    public function __construct(
        private string $publicKey = '',
        private string $privateKey = '',
        private string $inputKey = self::DEFAULT_INPUT_PARAM_KEY
    )
    {}
}
