<?php

declare(strict_types=1);

namespace Charcoal\Tests\Unit;

use Charcoal\ReCaptcha\Captcha;
use Charcoal\ReCaptcha\CaptchaConfig;
use Charcoal\ReCaptcha\CaptchaServiceProvider;
use Charcoal\ReCaptcha\LocalizedCaptcha;
use Charcoal\Tests\AbstractTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;

#[CoversClass(CaptchaServiceProvider::class)]
#[UsesClass(Captcha::class)]
#[UsesClass(CaptchaConfig::class)]
#[UsesClass(LocalizedCaptcha::class)]
final class CaptchaServiceProviderTest extends AbstractTestCase
{
    public function testCaptchaServiceProvider(): void
    {
        $provider  = new CaptchaServiceProvider();
        $container = $this->createContainer();

        $provider->register($container);

        $this->assertInstanceOf(CaptchaConfig::class, $container->get('charcoal/captcha/config'));
        $this->assertInstanceOf(Captcha::class, $container->get('charcoal/captcha'));
    }

    public function testLocalizedCaptchaServiceProvider(): void
    {
        $provider  = new CaptchaServiceProvider();
        $container = $this->createContainer();

        $provider->register($container);

        // TODO: Switch to Translator's service provider once PHP-DI supported.
        // (new TranslatorServiceProvider)->register($container);
        $container->set('translator', $this->createTranslator());

        $this->assertInstanceOf(LocalizedCaptcha::class, $container->get('charcoal/captcha'));
    }
}
