<?php

namespace Charcoal\Tests\Mock;

// From 'mcaskill/charcoal-recaptcha'
use Charcoal\ReCaptcha\CaptchaAwareTrait;

/**
 * Mock object for {@see \Charcoal\Tests\Unit\CaptchaAwareTest}
 */
class CaptchaAwareObject
{
    use CaptchaAwareTrait {
        CaptchaAwareTrait::setCaptcha as public;
        CaptchaAwareTrait::captcha as public;
    }
}
