<?php

namespace Charcoal\ReCaptcha;

// Dependencies from Pimple
use Pimple\Container;
use Pimple\ServiceProviderInterface;

// Dependency from Google
use ReCaptcha\ReCaptcha;

// Local dependency
use Charcoal\ReCaptcha\CaptchaConfig;

/**
 * Google reCAPTCHA Service Provider
 */
class CaptchaServiceProvider implements ServiceProviderInterface
{
    /**
     * Register Google reCAPTCHA.
     *
     * @param Container $container A DI container.
     * @return void
     */
    public function register(Container $container)
    {
        /**
         * Setup the Google reCaptcha service configuration.
         *
         * @param Container $container A container instance.
         * @return CaptchaConfig
         */
        $container['google/recaptcha/config'] = function (Container $container) {
            $appConfig = $container['config'];
            $captchaConfig = new CaptchaConfig($appConfig->get('google.recaptcha'));

            return $captchaConfig;
        };

        /**
         * Add the Google reCaptcha Service
         *
         * @param Container $container A container instance.
         * @return ReCaptcha
         */
        $container['google/recaptcha'] = function (Container $container) {
            return new ReCaptcha($container['google/recaptcha/config']['private_key']);
        };

        /**
         * Alias for retrieving the Google reCaptcha public key
         *
         * @param Container $container A container instance.
         * @return string
         */
        $container['google/recaptcha/public_key'] = $container->protect(function (Container $container) {
            return $container['google/recaptcha/config']['public_key'];
        });

        /**
         * Alias for retrieving the Google reCaptcha private key
         *
         * @param Container $container A container instance.
         * @return string
         */
        $container['google/recaptcha/private_key'] = $container->protect(function (Container $container) {
            return $container['google/recaptcha/config']['private_key'];
        });
    }
}
