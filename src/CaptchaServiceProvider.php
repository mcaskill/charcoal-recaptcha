<?php

declare(strict_types=1);

namespace Charcoal\ReCaptcha;

use Charcoal\ReCaptcha\Captcha;
use Charcoal\ReCaptcha\CaptchaConfig;
use Charcoal\ReCaptcha\CaptchaInterface;
use Charcoal\ReCaptcha\HtmlAwareCaptcha;
use Charcoal\ReCaptcha\HttpAwareCaptcha;
use Charcoal\ReCaptcha\LocalizedCaptcha;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Psr\Http\Message\ServerRequestInterface;
use ReCaptcha\ReCaptcha;

/**
 * Google reCAPTCHA Service Provider
 */
class CaptchaServiceProvider implements ServiceProviderInterface
{
    /**
     * Register Google reCAPTCHA.
     *
     * @param  Container $container A service container.
     * @return void
     */
    public function register(Container $container)
    {
        /**
         * Setup the Google reCaptcha service configuration.
         *
         * @param  Container $container A service container.
         * @return CaptchaConfig
         */
        $container['charcoal/captcha/config'] = function (Container $container): CaptchaConfig {
            $appConfig = $container['config'];

            return new CaptchaConfig($appConfig['apis.google.recaptcha']);
        };

        /**
         * Add the Charcoal reCaptcha Service
         *
         * @param  Container $container A service container.
         * @return CaptchaInterface
         */
        $container['charcoal/captcha'] = function (Container $container): CaptchaInterface {
            if (isset($container['translator'])) {
                $captcha = new LocalizedCaptcha(
                    $container['translator'],
                    $container['charcoal/captcha/config']
                );
            } else {
                $captcha = new Captcha(
                    $container['charcoal/captcha/config']
                );
            }

            if (class_exists(ServerRequestInterface::class)) {
                $captcha = new HttpAwareCaptcha($captcha);
            }

            return new HtmlAwareCaptcha($captcha);
        };
    }
}
