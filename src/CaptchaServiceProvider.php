<?php

namespace Charcoal\ReCaptcha;

// From Pimple
use Pimple\Container;
use Pimple\ServiceProviderInterface;

// From Google
use ReCaptcha\ReCaptcha;

// From 'charcoal-recaptcha'
use Charcoal\ReCaptcha\Captcha;
use Charcoal\ReCaptcha\CaptchaConfig;
use Charcoal\ReCaptcha\LocalizedCaptcha;

/**
 * Google reCAPTCHA Service Provider
 */
class CaptchaServiceProvider implements ServiceProviderInterface
{
    /**
     * Register Google reCAPTCHA.
     *
     * @param  Container $container A DI container.
     * @return void
     */
    public function register(Container $container)
    {
        /**
         * Setup the Google reCaptcha service configuration.
         *
         * @param  Container $container A container instance.
         * @return CaptchaConfig
         */
        $container['charcoal/captcha/config'] = function (Container $container) {
            $appConfig = $container['config'];

            return new CaptchaConfig($appConfig['apis.google.recaptcha']);
        };

        /**
         * Add the Google reCaptcha Client
         *
         * @param  Container $container A container instance.
         * @return ReCaptcha
         */
        $container['google/recaptcha'] = function (Container $container) {
            $captchaConfig = $container['charcoal/captcha/config'];
            return new ReCaptcha($captchaConfig['private_key']);
        };

        /**
         * Add the Charcoal reCaptcha Service
         *
         * @param  Container $container A container instance.
         * @return Captcha
         */
        $container['charcoal/captcha'] = function (Container $container) {
            if (isset($container['translator'])) {
                $captcha = new LocalizedCaptcha([
                    'config'     => $container['charcoal/captcha/config'],
                    'client'     => $container['google/recaptcha'],
                    'translator' => $container['translator']
                ]);
            } else {
                $captcha = new Captcha([
                    'config' => $container['charcoal/captcha/config'],
                    'client' => $container['google/recaptcha']
                ]);
            }

            return $captcha;
        };
    }
}
