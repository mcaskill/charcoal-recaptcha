<?php

namespace Charcoal\Tests\Unit;

use Charcoal\ReCaptcha\HtmlAwareCaptcha;
use Charcoal\Tests\AbstractTestCase;

/**
 * @coversDefaultClass \Charcoal\ReCaptcha\HtmlAwareCaptcha
 */
class HtmlAwareCaptchaTest extends AbstractTestCase
{
    /**
     * @var HtmlAwareCaptcha
     */
    private $captcha;

    /**
     * Create the HtmlAwareCaptcha instance.
     *
     * @return void
     */
    public function setUp(): void
    {
        $this->captcha = new HtmlAwareCaptcha($this->createWrapper($this->client));
    }

    /**
     * @covers ::getWidgetHtml
     * @covers ::buildAttributes
     * @return void
     */
    public function testGetWidgetHtml(): void
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
    public function testGetJsHtml(): void
    {
        // phpcs:disable Generic.Files.LineLength.TooLong
        $default      = '<script src="' . HtmlAwareCaptcha::CLIENT_API . '" async defer></script>';
        $withLang     = '<script src="' . HtmlAwareCaptcha::CLIENT_API . '?hl=jp" async defer></script>';
        $withCallback = '<script src="' . HtmlAwareCaptcha::CLIENT_API . '?render=explicit&onload=reOnloadCallback" async defer></script>';
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
     * @param  string     $expected    The expected rendered HTML.
     * @param  array|bool $attributes  The HTML attributes for the 'g-recaptcha' tag.
     * @param  array|bool $queryParams The query parameters for the JavaScript API link.
     * @return void
     */
    public function testDisplay(string $expected, $attributes, $queryParams): void
    {
        $actual = $this->captcha->display($attributes, $queryParams);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @used-by testDisplay
     * @return  array<string, array>
     */
    public function provideDisplayArgs(): array
    {
        // phpcs:disable Generic.Files.LineLength.TooLong
        return [
            'Default Display' => [
                '<script src="' . HtmlAwareCaptcha::CLIENT_API . '" async defer></script>' . "\n" .
                '<div class="g-recaptcha" data-sitekey="{site-key}"></div>',
                true, true
            ],
            'Without JS' => [
                '<div class="g-recaptcha" data-sitekey="{site-key}"></div>',
                true, false
            ],
            'Without Widget' => [
                '<script src="' . HtmlAwareCaptcha::CLIENT_API . '" async defer></script>',
                false, true
            ],
            'With Attributes' => [
                '<script src="' . HtmlAwareCaptcha::CLIENT_API . '?foo=baz" async defer></script>' . "\n" .
                '<div class="g-recaptcha" data-size="compact" data-sitekey="{site-key}"></div>',
                [ 'data-size' => 'compact' ], [ 'foo' => 'baz' ]
            ],
        ];
        // phpcs:enable
    }
}
