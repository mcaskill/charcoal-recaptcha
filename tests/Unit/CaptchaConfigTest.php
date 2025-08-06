<?php

declare(strict_types=1);

namespace Charcoal\Tests\Unit;

use Charcoal\ReCaptcha\CaptchaConfig;
use Charcoal\Tests\AbstractTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(CaptchaConfig::class)]
final class CaptchaConfigTest extends AbstractTestCase
{
    public function testDefaultConfig(): void
    {
        $config = new CaptchaConfig();

        $this->assertEquals('', $config->getPublicKey());
        $this->assertEquals('', $config->getPrivateKey());
        $this->assertEquals(CaptchaConfig::DEFAULT_INPUT_PARAM_KEY, $config->getInputKey());
    }

    public function testCreateFromArrayWithSnakeCase(): void
    {
        $array = [
            'public_key'  => '{site-key}',
            'private_key' => '{secret-key}',
            'input_key'   => '{input-key}',
        ];

        $config = CaptchaConfig::createFromArray($array);

        $this->assertEquals($array['public_key'], $config->getPublicKey());
        $this->assertEquals($array['private_key'], $config->getPrivateKey());
        $this->assertEquals($array['input_key'], $config->getInputKey());
    }

    public function testCreateFromArrayWithCamelCase(): void
    {
        $array = [
            'publicKey'  => '{site-key}',
            'privateKey' => '{secret-key}',
            'inputKey'   => '{input-key}',
        ];

        $config = CaptchaConfig::createFromArray($array);

        $this->assertEquals($array['publicKey'], $config->getPublicKey());
        $this->assertEquals($array['privateKey'], $config->getPrivateKey());
        $this->assertEquals($array['inputKey'], $config->getInputKey());
    }

    public function testCloneWithPublicKey(): void
    {
        $config = new CaptchaConfig('XXX', 'YYY', 'ZZZ');

        $newPublicKey = $config->withPublicKey('AAA');
        $this->assertNotSame($config, $newPublicKey);
        $this->assertEquals('XXX', $config->getPublicKey());
        $this->assertEquals('AAA', $newPublicKey->getPublicKey());
    }

    public function testCloneWithPrivateKey(): void
    {
        $config = new CaptchaConfig('XXX', 'YYY', 'ZZZ');

        $newPrivateKey = $config->withPrivateKey('BBB');
        $this->assertNotSame($config, $newPrivateKey);
        $this->assertEquals('YYY', $config->getPrivateKey());
        $this->assertEquals('BBB', $newPrivateKey->getPrivateKey());
    }

    public function testCloneWithInputKey(): void
    {
        $config = new CaptchaConfig('XXX', 'YYY', 'ZZZ');

        $newInputKey = $config->withInputKey('CCC');
        $this->assertNotSame($config, $newInputKey);
        $this->assertEquals('ZZZ', $config->getInputKey());
        $this->assertEquals('CCC', $newInputKey->getInputKey());
    }
}
