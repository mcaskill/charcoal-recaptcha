<?php

namespace Charcoal\Tests\Unit;

// From 'google/recaptcha'
use ReCaptcha\ReCaptcha;

// From 'mcaskill/charcoal-recaptcha'
use Charcoal\ReCaptcha\CaptchaAwareTrait;
use Charcoal\Tests\AbstractTestCase;
use Charcoal\Tests\Mock\CaptchaAwareObject;

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
    public function setUp()
    {
        $this->obj = new CaptchaAwareObject();
    }

    /**
     * @covers ::setCaptcha
     * @covers ::captcha
     *
     * @return void
     */
    public function testCaptchaAwareness()
    {
        $captcha = $this->createAdapter();

        $this->obj->setCaptcha($captcha);
        $this->assertSame($captcha, $this->obj->captcha());
    }

    /**
     * @expectedException        RuntimeException
     * @expectedExceptionMessage Missing Charcoal\ReCaptcha\Captcha adapter
     *
     * @covers ::captcha
     * @return void
     */
    public function testMissingCaptcha()
    {
        $this->obj->captcha();
    }
}
