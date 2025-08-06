<?php

declare(strict_types=1);

namespace Charcoal\Tests\Unit;

use Charcoal\ReCaptcha\CaptchaConfig;
use Charcoal\ReCaptcha\LocalizedCaptcha;
use Charcoal\Tests\AbstractTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;

#[CoversClass(LocalizedCaptcha::class)]
#[UsesClass(CaptchaConfig::class)]
final class LocalizedCaptchaTest extends AbstractTestCase
{
    private ?LocalizedCaptcha $captcha = null;

    /**
     * Create the Captcha instance.
     */
    public function setUp(): void
    {
        $this->captcha = new LocalizedCaptcha(
            $this->createConfig(),
            $this->createTranslator()
        );
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
     * @return array<list{0: string, 1: string}>
     */
    public static function provideErrorCodes(): array
    {
        // phpcs:disable Generic.Files.LineLength.TooLong
        return [
            'missing-input-secret'   => [ 'missing-input-secret',   'recaptcha.missing-input-secret' ],
            'invalid-input-secret'   => [ 'invalid-input-secret',   'recaptcha.invalid-input-secret' ],
            'missing-input'          => [ 'missing-input',          'recaptcha.missing-input-response' ],
            'missing-input-response' => [ 'missing-input-response', 'recaptcha.missing-input-response' ],
            'invalid-input'          => [ 'invalid-input',          'recaptcha.invalid-input-response' ],
            'invalid-input-response' => [ 'invalid-input-response', 'recaptcha.invalid-input-response' ],
            'unrecognized-error'     => [ 'unrecognized-error',     'recaptcha.error-code' ],
        ];
        // phpcs:enable
    }
}
