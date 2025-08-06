<?php

namespace Charcoal\ReCaptcha;

use Charcoal\ReCaptcha\Captcha;
use Charcoal\Translator\Translator;
use ReCaptcha\ReCaptcha;

/**
 * Service wrapper for the Google reCAPTCHA client
 *
 * This class is localized.
 */
class LocalizedCaptcha extends Captcha
{
    public function __construct(
        CaptchaConfig $config,
        private Translator $translator,
        ?ReCaptcha $client = null
    ) {
        parent::__construct($config, $client);
    }

    /**
     * Retrieve the localized message for the given error code.
     *
     * @link  https://developers.google.com/recaptcha/docs/verify
     * @param string $code An error code to resolve.
     */
    public function getErrorMessage(string $code): string
    {
        $translator = $this->translator;

        return match ($code) {
            'missing-input-secret',
            'invalid-input-secret' => $translator->trans('recaptcha.' . $code, [], 'validators'),

            'missing-input',
            'missing-input-response' => $translator->trans('recaptcha.missing-input-response', [], 'validators'),

            'invalid-input',
            'invalid-input-response' => $translator->trans('recaptcha.invalid-input-response', [], 'validators'),

            default => $translator->trans('recaptcha.error-code', [ '{code}' => $code ], 'validators'),
        };
    }
}
