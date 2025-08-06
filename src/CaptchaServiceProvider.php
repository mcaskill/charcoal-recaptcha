<?php

namespace Charcoal\ReCaptcha;

use Charcoal\ReCaptcha\Captcha;
use Charcoal\ReCaptcha\CaptchaConfig;
use Charcoal\ReCaptcha\LocalizedCaptcha;
use DI\Container;
use Psr\Container\ContainerInterface;

/**
 * Google reCAPTCHA Charcoal Service Provider
 */
class CaptchaServiceProvider
{
    /**
     * Register Google reCAPTCHA Charcoal services.
     *
     * @param Container $container A DI container.
     */
    public function register(ContainerInterface $container): void
    {
        /**
         * Register the Google reCAPTCHA service configuration.
         *
         * @param Container $container A container instance.
         */
        $container->set('charcoal/captcha/config', function (ContainerInterface $container): CaptchaConfig {
            $appConfig = $container->get('config');

            return CaptchaConfig::createFromArray($appConfig['apis.google.recaptcha']);
        });

        /**
         * Register the Charcoal reCAPTCHA handler.
         *
         * @param Container $container A container instance.
         */
        $container->set('charcoal/captcha', function (ContainerInterface $container): Captcha {
            if ($container->has('translator')) {
                return new LocalizedCaptcha(
                    $container->get('charcoal/captcha/config'),
                    $container->get('translator')
                );
            }

            return new Captcha($container->get('charcoal/captcha/config'));
        });
    }
}
