<?php

declare(strict_types=1);

namespace Charcoal\ReCaptcha;

use Charcoal\ReCaptcha\Captcha;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Localized service wrapper for the Google reCAPTCHA client
 */
class LocalizedCaptcha extends Captcha
{
    /**
     * The translator service.
     *
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @param TranslatorInterface   $translator The translator service.
     * @param Config|array|object   $config     The service wrapper settings.
     * @param ReCaptcha|string|null $client     Optional. The reCAPTCHA service instance or class name.
     */
    public function __construct(TranslatorInterface $translator, $config, $client = null)
    {
        $this->translator = $translator;

        parent::__construct($config, $client);
    }

    /**
     * Retrieve the message for the given error code.
     *
     * @link   https://developers.google.com/recaptcha/docs/verify
     * @param  string|int $code An error code to resolve.
     * @return string
     */
    public function getErrorMessage($code): string
    {
        switch ($code) {
            case 'missing-input-secret':
            case 'invalid-input-secret':
                return $this->translator->trans('recaptcha.' . $code, [], 'validators');

            case 'missing-input':
            case 'missing-input-response':
                return $this->translator->trans('recaptcha.missing-input-response', [], 'validators');

            case 'invalid-input':
            case 'invalid-input-response':
                return $this->translator->trans('recaptcha.invalid-input-response', [], 'validators');
        }

        return $this->translator->trans('recaptcha.error-code', [
            '{code}' => $code,
        ], 'validators');
    }
}
