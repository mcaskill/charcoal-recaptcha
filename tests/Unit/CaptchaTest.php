<?php

namespace Charcoal\Tests\Unit;

use Charcoal\ReCaptcha\Captcha;
use Charcoal\ReCaptcha\CaptchaConfig;
use Charcoal\Tests\AbstractTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;
use ReCaptcha\ReCaptcha;

#[CoversClass(Captcha::class)]
#[UsesClass(CaptchaConfig::class)]
class CaptchaTest extends AbstractTestCase
{
    private ?Captcha $captcha = null;

    private ?ReCaptcha $client = null;

    private ?CaptchaConfig $config = null;

    /**
     * Create the Captcha instance.
     */
    public function setUp(): void
    {
        $this->captcha = new Captcha(
            ($this->config = $this->createConfig()),
            ($this->client = $this->createClient())
        );
    }

    public function testConstruct(): void
    {
        $this->assertSame($this->client, $this->captcha->getClient());
        $this->assertSame($this->config, $this->captcha->getConfig());
    }

    public function testGetErrorMessages(): void
    {
        $expectations = self::provideErrorCodes();

        $messages = $this->captcha->getErrorMessages(array_column($expectations, 0));
        $this->assertEquals(array_column($expectations, 1, 0), $messages);
    }

    /**
     * @param string $code     The error code.
     * @param string $expected The expected error message.
     */
    #[DataProvider('provideErrorCodes')]
    public function testGetErrorMessage(string $code, string $expected): void
    {
        $message = $this->captcha->getErrorMessage($code);
        $this->assertEquals($expected, $message);
    }

    /**
     * @used-by testGetErrorMessage
     * @return array<string, list{0: string, 1: string}>
     */
    public static function provideErrorCodes(): array
    {
        // phpcs:disable Generic.Files.LineLength.TooLong
        return [
            'missing-input-secret'   => [ 'missing-input-secret',   'The reCAPTCHA secret parameter is missing.' ],
            'invalid-input-secret'   => [ 'invalid-input-secret',   'The reCAPTCHA secret parameter is invalid or malformed.' ],
            'missing-input'          => [ 'missing-input',          'The CAPTCHA response parameter is missing.' ],
            'missing-input-response' => [ 'missing-input-response', 'The CAPTCHA response parameter is missing.' ],
            'invalid-input'          => [ 'invalid-input',          'The CAPTCHA response parameter is invalid or malformed.' ],
            'invalid-input-response' => [ 'invalid-input-response', 'The CAPTCHA response parameter is invalid or malformed.' ],
            'unrecognized-error'     => [ 'unrecognized-error',     'Unknown reCAPTCHA error: unrecognized-error' ],
        ];
        // phpcs:enable
    }

    public function testGetWidgetHtml(): void
    {
        // phpcs:disable Generic.Files.LineLength.TooLong
        $default   = '<div class="g-recaptcha" data-sitekey="{site-key}"></div>';
        $withAttrs = '<div class="g-recaptcha" data-theme="light" data-sitekey="{site-key}"></div>';
        // phpcs:enable

        $this->assertEquals($default, $this->captcha->getWidgetHtml());
        $this->assertEquals($withAttrs, $this->captcha->getWidgetHtml([ 'data-theme' => 'light' ]));
    }

    public function testGetJsHtml(): void
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
     * @param string $expected    The expected rendered HTML.
     * @param mixed  $attributes  The HTML attributes for the 'g-recaptcha' tag.
     * @param mixed  $queryParams The query parameters for the JavaScript API link.
     */
    #[DataProvider('provideDisplayArgs')]
    public function testDisplay(string $expected, mixed $attributes, mixed $queryParams): void
    {
        $actual = $this->captcha->display($attributes, $queryParams);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @used-by testDisplay
     * @return array<string, list{0: string, 1: string, array<string,mixed>|bool, array<string,mixed>|bool}>
     */
    public static function provideDisplayArgs(): array
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
