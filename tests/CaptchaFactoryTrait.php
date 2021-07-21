<?php

namespace Charcoal\Tests;

use Charcoal\ReCaptcha\Captcha;
use ReCaptcha\ReCaptcha;

/**
 * Captcha Factory Utilities
 */
trait CaptchaFactoryTrait
{
    /**
     * @var array<string, string>
     */
    private $config = [
        'public_key'  => '{site-key}',
        'private_key' => '{secret-key}',
    ];

    /**
     * Create the ReCaptcha client.
     *
     * @return ReCaptcha
     */
    protected function createClient(): ReCaptcha
    {
        return new ReCaptcha($this->getConfig('private_key'));
    }

    /**
     * Create the service wrapper.
     *
     * @param  ReCaptcha|null $client The ReCaptcha client.
     * @return Captcha
     */
    protected function createWrapper(ReCaptcha $client = null): Captcha
    {
        return new Captcha(
            $this->getConfig(),
            ($client ?? $this->createClient())
        );
    }

    /**
     * Returns the reCAPTCHA configset.
     *
     * @param  string|null $key If provided, return the key's value.
     * @return mixed
     */
    protected function getConfig(string $key = null)
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
