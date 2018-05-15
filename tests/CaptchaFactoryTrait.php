<?php

namespace Charcoal\Tests;

// From 'google/recaptcha'
use ReCaptcha\ReCaptcha;

// From 'mcaskill/charcoal-recaptcha'
use Charcoal\ReCaptcha\Captcha;

/**
 * Captcha Factory Utilities
 */
trait CaptchaFactoryTrait
{
    /**
     * @var array
     */
    private $config = [
        'public_key'  => '{site-key}',
        'private_key' => '{secret-key}',
    ];

    /**
     * Create the Captcha adapter.
     *
     * @param  ReCaptcha|null $client The ReCaptcha client.
     * @return Captcha
     */
    protected function createAdapter(ReCaptcha $client = null)
    {
        return new Captcha([
            'config' => $this->getConfig(),
            'client' => $client === null ? $this->createClient() : $client,
        ]);
    }

    /**
     * Create the ReCaptcha client.
     *
     * @return ReCaptcha
     */
    protected function createClient()
    {
        return new ReCaptcha($this->getConfig('private_key'));
    }

    /**
     * Returns the reCAPTCHA configset.
     *
     * @param  string|null $key If provided, return the key's value.
     * @return mixed
     */
    protected function getConfig($key = null)
    {
        if ($key !== null) {
            if (isset($this->config[$key])) {
                return $this->config[$key];
            } else {
                throw new \InvalidArgumentException(
                    sprintf('Config key "%s" is not defined', $key)
                );
            }
        }

        return $this->config;
    }
}
