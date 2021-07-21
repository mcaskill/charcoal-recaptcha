<?php

namespace Charcoal\Tests\Unit;

use Charcoal\Config\ConfigInterface;
use Charcoal\ReCaptcha\Captcha;
use Charcoal\Tests\AbstractTestCase;
use ReCaptcha\ReCaptcha;

/**
 * @coversDefaultClass \Charcoal\ReCaptcha\Captcha
 */
class CaptchaTest extends AbstractTestCase
{
    /**
     * @var Captcha
     */
    private $captcha;

    /**
     * @var ReCaptcha
     */
    private $client;

    /**
     * Create the Captcha instance.
     *
     * @return void
     */
    public function setUp(): void
    {
        $this->client  = $this->createClient();
        $this->captcha = $this->createWrapper($this->client);
    }

    /**
     * @covers ::__construct
     * @covers ::getClient
     * @covers ::getConfig
     * @return void
     */
    public function testConstruct(): void
    {
        $this->assertSame($this->client, $this->captcha->getClient());
        $this->assertInstanceOf(ConfigInterface::class, $this->captcha->getConfig());
    }
}
