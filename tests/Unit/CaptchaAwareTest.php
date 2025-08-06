<?php

declare(strict_types=1);

namespace Charcoal\Tests\Unit;

use Charcoal\ReCaptcha\Captcha;
use Charcoal\ReCaptcha\CaptchaConfig;
use Charcoal\ReCaptcha\CaptchaAwareTrait;
use Charcoal\Tests\AbstractTestCase;
use Charcoal\Tests\Mock\CaptchaAwareObject;
use PHPUnit\Framework\Attributes\CoversTrait;
use PHPUnit\Framework\Attributes\UsesClass;
use TypeError;

#[CoversTrait(CaptchaAwareTrait::class)]
#[UsesClass(Captcha::class)]
#[UsesClass(CaptchaConfig::class)]
final class CaptchaAwareTest extends AbstractTestCase
{
    private ?CaptchaAwareObject $obj = null;

    protected function setUp(): void
    {
        $this->obj ??= new CaptchaAwareObject();
    }

    public function testCaptchaAwareness(): void
    {
        $captcha = new Captcha($this->createConfig());

        $this->obj->setCaptcha($captcha);
        $this->assertSame($captcha, $this->obj->getCaptcha());
    }

    public function testMissingCaptcha(): void
    {
        $this->expectException(TypeError::class);
        $this->expectExceptionMessage('Return value must be of type Charcoal\ReCaptcha\Captcha, null returned');

        $this->obj->getCaptcha();
    }
}
