<?php

namespace Charcoal\ReCaptcha;

use RuntimeException;

// From 'charcoal-recaptcha'
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
     * @throws RuntimeException If the CAPTCHA service was not previously set.
     * @return Captcha
     */
    public function captcha()
    {
        if (!isset($this->captcha)) {
            throw new RuntimeException(sprintf(
                'CAPTCHA is not defined for "%s"',
                get_class($this)
            ));
        }

        return $this->captcha;
    }

    /**
     * Set a CAPTCHA validation service.
     *
     * @param  Captcha $captcha The CAPTCHA service.
     * @return void
     */
    protected function setCaptcha(Captcha $captcha)
    {
        $this->captcha = $captcha;
    }
}
