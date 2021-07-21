<?php

declare(strict_types=1);

namespace Charcoal\ReCaptcha;

use Charcoal\ReCaptcha\CaptchaInterface;
use ReCaptcha\ReCaptcha;
use ReCaptcha\Response as ApiResponse;

/**
 * Decorator for the service wrapper of the Google reCAPTCHA client
 */
abstract class AbstractCaptchaDecorator implements CaptchaInterface
{
    /**
     * Store the service wrapper.
     *
     * @var CaptchaInterface
     */
    private $wrapper;

    /**
     * @param Captcha $wrapper The service wrapper.
     */
    public function __construct(CaptchaInterface $wrapper)
    {
        $this->wrapper = $wrapper;
    }

    /**
     * Call the service wrapper.
     *
     * If the returned value is the service wrapper (fluent interface),
     * return the current decorator instead.
     *
     * @param  string  $method The name of the method being called.
     * @param  mixed[] $args   The arguments passed to the method.
     * @return mixed The return value of the called method.
     */
    public function __call(string $method, array $args = [])
    {
        $result = $this->wrapper->{$method}(...$args);

        if ($result === $this->wrapper) {
            return $this;
        }

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function verify(string $input, ?string $remoteIp = null): bool
    {
        return $this->wrapper->verify($input, $remoteIp);
    }

    /**
     * @inheritDoc
     */
    public function getClient(): ReCaptcha
    {
        return $this->wrapper->getClient();
    }

    /**
     * @inheritDoc
     */
    public function getConfig(string $key = null, $default = null)
    {
        return $this->wrapper->getConfig($key, $default);
    }

    /**
     * @inheritDoc
     */
    public function getLastResponse(): ?ApiResponse
    {
        return $this->wrapper->getLastResponse();
    }

    /**
     * @inheritDoc
     */
    public function getLastErrorCodes(): ?iterable
    {
        return $this->wrapper->getLastErrorCodes();
    }

    /**
     * @inheritDoc
     */
    public function getLastErrorMessages(): ?iterable
    {
        return $this->wrapper->getLastErrorMessages();
    }

    /**
     * @inheritDoc
     */
    public function getErrorMessages(iterable $codes): iterable
    {
        return $this->wrapper->getErrorMessages($codes);
    }

    /**
     * @inheritdoc
     */
    public function getErrorMessage($code): string
    {
        return $this->wrapper->getErrorMessage($code);
    }
}
