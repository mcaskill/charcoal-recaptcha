<?php

namespace Charcoal\Tests;

use Charcoal\ReCaptcha\CaptchaConfig;
use Charcoal\Translator\LocalesManager;
use Charcoal\Translator\Translator;
use DI\Container;
use ReCaptcha\ReCaptcha;

/**
 * Captcha Factory Utilities
 */
trait CaptchaFactoryTrait
{
    /**
     * @var array{
     *     public_key: string,
     *     private_key: string,
     * }
     */
    private array $config = [
        'public_key'  => '{site-key}',
        'private_key' => '{secret-key}',
        'input_key'   => '{input-key}',
    ];

    /**
     * Create the ReCaptcha client.
     */
    protected function createClient(): ReCaptcha
    {
        return new ReCaptcha($this->config['private_key']);
    }

    /**
     * Create the Captcha config.
     */
    protected function createConfig(): CaptchaConfig
    {
        return CaptchaConfig::createFromArray($this->config);
    }

    protected function createContainer(): Container
    {
        return new Container([
            'config' => [
                'apis.google.recaptcha' => $this->config,
            ],
        ]);
    }

    /**
     * Create the Translator service.
     */
    protected function createTranslator(): Translator
    {
        return new Translator([
            'manager' => new LocalesManager([
                'locales' => [
                    'en' => [],
                ],
            ]),
        ]);
    }

    protected function getConfig(): array
    {
        return $this->config;
    }
}
