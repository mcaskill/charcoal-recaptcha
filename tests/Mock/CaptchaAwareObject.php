<?php

namespace Charcoal\Tests\Mock;

use Charcoal\ReCaptcha\CaptchaAwareTrait;

/**
 * Mock object for {@see \Charcoal\Tests\Unit\CaptchaAwareTest}
 */
class CaptchaAwareObject
{
    use CaptchaAwareTrait {
        CaptchaAwareTrait::getCaptcha as public;
        CaptchaAwareTrait::setCaptcha as public;
    }
}
