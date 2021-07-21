<?php

declare(strict_types=1);

namespace Charcoal\ReCaptcha;

use Charcoal\ReCaptcha\Captcha;

/**
 * Google ReCaptcha Service
 */
trait CaptchaAwareTrait
{
    /**
     * Store the reCAPTCHA service instance.
     *
     * @var Captcha
     */
    private $captcha;

    /**
     * Retrieve the CAPTCHA validation service.
     *
     * @return Captcha
     */
    public function getCaptcha(): Captcha
    {
        return $this->captcha;
    }

    /**
     * Set a CAPTCHA validation service.
     *
     * @param  Captcha $captcha The CAPTCHA service.
     * @return void
     */
    protected function setCaptcha(Captcha $captcha): void
    {
        $this->captcha = $captcha;
    }
}
