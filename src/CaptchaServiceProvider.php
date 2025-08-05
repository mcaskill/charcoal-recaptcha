<?php

namespace Charcoal\ReCaptcha;

// From Google
use ReCaptcha\ReCaptcha;
// From 'charcoal-recaptcha'
use Charcoal\ReCaptcha\Captcha;
use Charcoal\ReCaptcha\CaptchaConfig;
use Charcoal\ReCaptcha\LocalizedCaptcha;
use Psr\Container\ContainerInterface;
use DI\Container;

/**
 * Google reCAPTCHA Service Provider
 */
class CaptchaServiceProvider
{
    /**
     * Register Google reCAPTCHA.
     *
     * @param  Container $container A DI container.
     * @return void
     */
    public function register(ContainerInterface $container)
    {
        /**
         * Setup the Google reCaptcha service configuration.
         *
         * @param  Container $container A container instance.
         * @return CaptchaConfig
         */
        $container->set('charcoal/captcha/config', function (ContainerInterface $container) {
            $appConfig = $container->get('config');

            return new CaptchaConfig($appConfig['apis.google.recaptcha']);
        });

        /**
         * Add the Charcoal reCaptcha Service
         *
         * @param  Container $container A container instance.
         * @return Captcha
         */
        $container->set('charcoal/captcha', function (ContainerInterface $container) {
            $args = [
                'config' => $container->get('charcoal/captcha/config')
            ];

            if ($container->has('translator')) {
                $args['translator'] = $container->get('translator');
                $captcha = new LocalizedCaptcha($args);
            } else {
                $captcha = new Captcha($args);
            }

            return $captcha;
        });
    }
}
