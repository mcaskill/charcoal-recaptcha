<?php

namespace Charcoal\Tests\Unit;

// From 'google/recaptcha'
use ReCaptcha\ReCaptcha;

// From 'locomotivemtl/charcoal-config'
use Charcoal\Config\ConfigInterface;

// From 'mcaskill/charcoal-recaptcha'
use Charcoal\ReCaptcha\Captcha;
use Charcoal\Tests\AbstractTestCase;

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
    public function setUp()
    {
        $this->client  = $this->createClient();
        $this->captcha = $this->createAdapter($this->client);
    }

    /**
     * @covers ::__construct
     * @covers ::setClient
     * @covers ::client
     * @covers ::setConfig
     * @covers ::config
     * @covers ::createConfig
     * @return void
     */
    public function testConstruct()
    {
        $this->assertSame($this->client, $this->captcha->client());
        $this->assertInstanceOf(ConfigInterface::class, $this->captcha->config());
    }

    /**
     * @covers ::getWidgetHtml
     * @covers ::buildAttributes
     * @return void
     */
    public function testGetWidgetHtml()
    {
        // phpcs:disable Generic.Files.LineLength.TooLong
        $default   = '<div class="g-recaptcha" data-sitekey="{site-key}"></div>';
        $withAttrs = '<div class="g-recaptcha" data-theme="light" data-sitekey="{site-key}"></div>';
        // phpcs:enable

        $this->assertEquals($default, $this->captcha->getWidgetHtml());
        $this->assertEquals($withAttrs, $this->captcha->getWidgetHtml([ 'data-theme' => 'light' ]));
    }

    /**
     * @covers ::getJsHtml
     * @covers ::getJsUri
     * @return void
     */
    public function testGetJsHtml()
    {
        // phpcs:disable Generic.Files.LineLength.TooLong
        $default      = '<script src="' . Captcha::CLIENT_API . '" async defer></script>';
        $withLang     = '<script src="' . Captcha::CLIENT_API . '?hl=jp" async defer></script>';
        $withCallback = '<script src="' . Captcha::CLIENT_API . '?render=explicit&onload=reOnloadCallback" async defer></script>';
        // phpcs:enable

        $this->assertEquals($default, $this->captcha->getJsHtml());
        $this->assertEquals($withLang, $this->captcha->getJsHtml([ 'lang' => 'jp' ]));
        $this->assertEquals($withCallback, $this->captcha->getJsHtml([
            'render' => 'explicit',
            'onload' => 'reOnloadCallback',
        ]));
    }

    /**
     * @dataProvider provideDisplayArgs
     * @covers ::display
     *
     * @param  string $expected    The expected rendered HTML.
     * @param  mixed  $attributes  The HTML attributes for the 'g-recaptcha' tag.
     * @param  mixed  $queryParams The query parameters for the JavaScript API link.
     * @return void
     */
    public function testDisplay($expected, $attributes, $queryParams)
    {
        $actual = $this->captcha->display($attributes, $queryParams);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @used-by testDisplay
     * @return  array
     */
    public function provideDisplayArgs()
    {
        // phpcs:disable Generic.Files.LineLength.TooLong
        return [
            'Default Display' => [
                '<script src="' . Captcha::CLIENT_API . '" async defer></script>' . "\n" .
                '<div class="g-recaptcha" data-sitekey="{site-key}"></div>',
                true, true
            ],
            'Without JS' => [
                '<div class="g-recaptcha" data-sitekey="{site-key}"></div>',
                true, false
            ],
            'Without Widget' => [
                '<script src="' . Captcha::CLIENT_API . '" async defer></script>',
                false, true
            ],
            'With Attributes' => [
                '<script src="' . Captcha::CLIENT_API . '?foo=baz" async defer></script>' . "\n" .
                '<div class="g-recaptcha" data-size="compact" data-sitekey="{site-key}"></div>',
                [ 'data-size' => 'compact' ], [ 'foo' => 'baz' ]
            ],
        ];
        // phpcs:enable
    }
}
