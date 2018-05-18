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
         * Add the Charcoal reCaptcha Service
         *
         * @param  Container $container A container instance.
         * @return Captcha
         */
        $container['charcoal/captcha'] = function (Container $container) {
            $args = [
                'config' => $container['charcoal/captcha/config']
            ];

            if (isset($container['translator'])) {
                $args['translator'] = $container['translator'];
                $captcha = new LocalizedCaptcha($args);
            } else {
                $captcha = new Captcha($args);
            }

            return $captcha;
        };
    }
}
