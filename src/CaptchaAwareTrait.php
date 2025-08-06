<?php

namespace Charcoal\ReCaptcha;

use Charcoal\ReCaptcha\Captcha;

/**
 * Google ReCaptcha Service
 */
trait CaptchaAwareTrait
{
    /**
     * Store the reCAPTCHA service instance.
     */
    private ?Captcha $captcha = null;

    /**
     * Retrieve the CAPTCHA validation service.
     */
    public function getCaptcha(): Captcha
    {
        return $this->captcha;
    }

    /**
     * Set a CAPTCHA validation service.
     */
    protected function setCaptcha(Captcha $captcha): void
    {
        $this->captcha = $captcha;
    }
}
