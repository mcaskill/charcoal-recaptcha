<?php

namespace Charcoal\ReCaptcha;

use Pimple\Container;

// Dependencies from Google
use ReCaptcha\ReCaptcha;
use ReCaptcha\Response;

/**
 * Google ReCaptcha
 *
 * ## Dependencies
 *
 * - {@see ReCaptcha\ReCaptcha}
 * - {@see Charcoal\Translation\Catalog\CatalogAwareInterface}
 */
trait CaptchaAwareTrait
{
    /**
     * Store the reCAPTCHA service instance.
     *
     * @var ReCaptcha
     */
    private $captcha;

    /**
     * Form control name and  POST parameter when the user submits the form on your site.
     *
     * @var string
     */
    private $captchaFieldName;

    /**
     * The end user's IP address..
     *
     * @var string
     */
    private $captchaRemoteIp;

    /**
     * Store the last instance of the reCAPTCHA response.
     *
     * @var Response
     */
    private $lastCaptchaResponse;

    /**
     * Inject dependencies from a DI Container.
     *
     * @param Container $container A dependencies container instance.
     * @return void
     */
    public function setCaptchaAwareTraitDependencies(Container $container)
    {
        $this->captchaFieldName = $container['google/recaptcha/config']['field'];

        $this->setCaptcha($container['google/recaptcha']);
    }

    /**
     * Set a CAPTCHA validation service.
     *
     * @param ReCaptcha $captcha The CAPTCHA service.
     * @return self
     */
    public function setCaptcha(ReCaptcha $captcha)
    {
        $this->captcha = $captcha;

        return $this;
    }

    /**
     * Retrieve the CAPTCHA validation service.
     *
     * @throws Exception If the CAPTCHA service was not previously set.
     * @return ReCaptcha
     */
    public function captcha()
    {
        if (!isset($this->captcha)) {
            throw new Exception(
                sprintf('CAPTCHA is not defined for "%s"', get_class($this))
            );
        }

        return $this->captcha;
    }

    /**
     * Validate Google's ReCaptcha response.
     *
     * @param  string            $input    The "g-captcha-response" field from the form submission.
     * @param  array|Traversable $feedback If $feedback is provided, then it is filled with any validation messages.
     * @return boolean Returns TRUE if the ReCaptcha was successful, otherwise an array of messages.
     * @todo   Implement {@see Charcoal\Validator\ValidatorResult}.
     */
    public function validateCaptcha($input = null, &$feedback = [])
    {
        if (is_string($input) && strlen($input)) {
            $input = filter_var($input, FILTER_UNSAFE_RAW);
        } else {
            $field = (isset($this->captchaFieldName) ? $this->captchaFieldName : CaptchaConfig::DEFAULT_FIELD_NAME );
            $input = filter_input(INPUT_POST, $field, FILTER_UNSAFE_RAW);
        }

        $remoteIp = (isset($this->captchaRemoteIp) ? $this->captchaRemoteIp : getenv('REMOTE_ADDR'));
        $response = $this->captcha->verify($input, $remoteIp);

        $this->lastCaptchaResponse = $response;

        $this->parseCaptchaResponseCodes($response, $feedback);

        return $response->isSuccess();
    }

    /**
     * Parse error codes for verion 2.0 of the ReCaptcha API.
     *
     * @link https://developers.google.com/recaptcha/docs/verify  Error code reference
     * @link https://gist.github.com/mcaskill/02029dcfe8bb660fcbb0  Source of function
     *
     * @param  null|Response     $response A reCAPTCHA Response object.
     * @param  array|Traversable $feedback If the second parameter `$feedback` is present, error messages are stored
     *     in this variable as associative array elements instead.
     * @throws Exception If a reCAPTCHA response object is not provided or available.
     * @return array  Returns an array of error codes and messages.
     */
    public function parseCaptchaResponseCodes(Response $response = null, &$feedback = [])
    {
        $c = $this->catalog();

        $msgRequestHelp   = $c->translate('recaptcha.help');
        $msgHumanOnly     = $c->translate('recaptcha.is-robot');
        $msgUnknownError  = $c->translate('recaptcha.unknown').' '.$msgRequestHelp;
        $msgSuccessful    = $c->translate('recaptcha.valid');
        $codeUnknownError = 'unknown-error-response';
        $codeSuccessful   = 'unknown-error-response';

        if ($response === null) {
            if ($this->lastCaptchaResponse instanceof Response) {
                $response = $this->lastCaptchaResponse;
            } else {
                throw new Exception('A reCAPTCHA Response object is required.');
            }
        }

        if ($response->isSuccess()) {
            $feedback[$codeSuccessful] = $msgSuccessful;
            return $feedback;
        }

        if (!$response->getErrorCodes()) {
            $feedback[$codeUnknownError] = $msgUnknownError;
            return $feedback;
        }

        $codes = $response->getErrorCodes();

        if (!is_array($codes)) {
            $codes = [ $codes ];
        }

        $codes = array_filter($codes, 'strlen');

        if (!count($codes)) {
            $feedback[$codeUnknownError] = $msgUnknownError;
            return $feedback;
        }

        foreach ($codes as $code) {
            switch ($code) {
                case 'missing-input-secret':
                case 'invalid-input-secret':
                    $feedback[$code] = $c->translate('recaptcha'.$code).' '.$msgRequestHelp;
                    break;

                case 'missing-input':
                case 'missing-input-response':
                    $feedback[$code] = $c->translate('recaptcha.missing-input-response').' '.$msgHumanOnly;
                    break;

                case 'invalid-input':
                case 'invalid-input-response':
                    $feedback[$code] = $c->translate('recaptcha.invalid-input-response').' '.$msgHumanOnly;
                    break;

                default:
                    $feedback[$code] = $msgUnknownError;
                    break;
            }
        }

        return $feedback;
    }
}
