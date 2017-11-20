<?php

namespace Charcoal\ReCaptcha;

use Exception;
use RuntimeException;

// From 'charcoal-translator'
use Charcoal\Translator\TranslatorAwareTrait;

// From 'charcoal-recaptcha'
use Charcoal\ReCaptcha\Captcha;

/**
 * Service wrapper for the Google reCAPTCHA client
 *
 * This class is localized.
 */
class LocalizedCaptcha extends Captcha
{
    use TranslatorAwareTrait;

    /**
     * @param  array $data The constructor options.
     * @return void
     */
    public function __construct(array $data)
    {
        parent::__construct($data);

        $this->setTranslator($data['translator']);
    }

    /**
     * Retrieve the message for the given error code.
     *
     * @link   https://developers.google.com/recaptcha/docs/verify
     * @param  string $code An error code to resolve.
     * @return array
     */
    protected function getErrorMessage($code)
    {
        switch ($code) {
            case 'missing-input-secret':
            case 'invalid-input-secret':
                return $this->translator()->trans('recaptcha.' . $code, [], 'validators');

            case 'missing-input':
            case 'missing-input-response':
                return $this->translator()->trans('recaptcha.missing-input-response', [], 'validators');

            case 'invalid-input':
            case 'invalid-input-response':
                return $this->translator()->trans('recaptcha.invalid-input-response', [], 'validators');

            default:
                return $this->translator()->trans('recaptcha.error-code', [
                    '{code}' => $code
                ], 'validators');
        }
    }
}
