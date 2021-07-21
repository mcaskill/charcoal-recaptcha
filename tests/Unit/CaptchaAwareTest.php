<?php

namespace Charcoal\Tests\Unit;

use Charcoal\ReCaptcha\CaptchaAwareTrait;
use Charcoal\Tests\AbstractTestCase;
use Charcoal\Tests\Mock\CaptchaAwareObject;
use ReCaptcha\ReCaptcha;

/**
 * @coversDefaultClass \Charcoal\ReCaptcha\CaptchaAwareTrait
 */
class CaptchaAwareTest extends AbstractTestCase
{
    /**
     * @var CaptchaAwareObject
     */
    private $obj;

    /**
     * Create the Captcha instance.
     *
     * @return void
     */
    public function setUp(): void
    {
        $this->obj = new CaptchaAwareObject();
    }

    /**
     * @covers ::setCaptcha
     * @covers ::getCaptcha
     *
     * @return void
     */
    public function testCaptchaAwareness(): void
    {
        $captcha = $this->createWrapper();

        $this->obj->setCaptcha($captcha);
        $this->assertSame($captcha, $this->obj->getCaptcha());
    }

    /**
     * @expectedException        RuntimeException
     * @expectedExceptionMessage Missing Charcoal\ReCaptcha\Captcha adapter
     *
     * @covers ::getCaptcha
     * @return void
     */
    public function testMissingCaptcha(): void
    {
        $this->obj->getCaptcha();
    }
}
