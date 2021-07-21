<?php

declare(strict_types=1);

namespace Charcoal\ReCaptcha;

use Charcoal\ReCaptcha\CaptchaConfig as Config;
use Charcoal\ReCaptcha\CaptchaInterface;
use Charcoal\ReCaptcha\Exceptions\InvalidArgumentException;
use Charcoal\ReCaptcha\Exceptions\UnexpectedValueException;
use Closure;
use ReCaptcha\ReCaptcha;
use ReCaptcha\Response as ApiResponse;
use Throwable;

use function is_array;
use function is_null;
use function is_object;
use function is_string;
use function sprintf;
use function strtr;

/**
 * Simple service wrapper for the Google reCAPTCHA client
 *
 * This class can be used as a PSR-7 middleware or as an object in your service layer.
 */
class Captcha implements CaptchaInterface
{
    /**
     * Store the settings.
     *
     * @var Config
     */
    private $config;

    /**
     * Store the ReCaptcha client.
     *
     * @var ReCaptcha
     */
    private $client;

    /**
     * Default "ReCaptcha" class to use for making a new client.
     *
     * @var string
     */
    private $clientClass = ReCaptcha::class;

    /**
     * Store the last ReCaptcha response.
     *
     * @var ApiResponse
     */
    private $lastResponse;

    /**
     * @param  Config|array|object   $config The service wrapper settings.
     * @param  ReCaptcha|string|null $client Optional. The reCAPTCHA service instance or class name.
     * @throws InvalidArgumentException If the $config is invalid.
     */
    public function __construct($config, $client = null)
    {
        if ($config instanceof Config) {
            $this->config = $config;
        } elseif (is_array($config) || is_object($config)) {
            $this->config = new Config($config);
        } else {
            throw new InvalidArgumentException(sprintf(
                'Expected first parameter to be %s',
                Config::class
            ));
        }

        if ($client) {
            if ($client instanceof ReCaptcha) {
                $this->client = $client;
            } elseif (is_string($client)) {
                $this->clientClass = $client;
            } else {
                throw new InvalidArgumentException(sprintf(
                    'Expected second parameter to be %s',
                    ReCaptcha::class
                ));
            }
        }
    }

    /**
     * Retrieve the ReCaptcha client.
     *
     * @return ReCaptcha
     */
    public function getClient(): ReCaptcha
    {
        if (is_null($this->client)) {
            $this->client = $this->createClient();
        }

        return $this->client;
    }

    /**
     * Retrieve the CAPTCHA configset or a specific key from the container.
     *
     * @param  string|null $key     Optional. If provided, the data key to retrieve.
     * @param  mixed       $default Optional. The fallback value to return if $key does not exist.
     * @return mixed If $key is NULL, the Config object is returned.
     *     If $key is given, its value on the Config object is returned.
     *     If the value of $key is NULL, the value of $default is returned.
     */
    public function getConfig(string $key = null, $default = null)
    {
        if (is_string($key)) {
            if ($this->config->has($key)) {
                return $this->config->get($key);
            } elseif ($default instanceof Closure) {
                return $default();
            } else {
                return $default;
            }
        }

        return $this->config;
    }

    /**
     * Call the reCAPTCHA API to verify whether the user passes CAPTCHA test.
     *
     * @param  string      $input    The value of 'g-recaptcha-response' in the submitted form.
     * @param  string|null $remoteIp Optional. The end user's IP address.
     * @return bool Returns TRUE if 'g-recaptcha-response' is valid, FALSE otherwise.
     */
    public function verify(string $input, ?string $remoteIp = null): bool
    {
        $this->lastResponse = $this->getClient()->verify($input, $remoteIp);

        return $this->lastResponse->isSuccess();
    }

    /**
     * Retrieve the ReCaptcha response from the last CAPTCHA verification.
     *
     * @return ?ApiResponse
     */
    public function getLastResponse(): ?ApiResponse
    {
        return $this->lastResponse;
    }

    /**
     * Retrieve the error codes from the last CAPTCHA verification.
     *
     * @return ?(string|int)[]
     */
    public function getLastErrorCodes(): ?iterable
    {
        $response = $this->getLastResponse();
        if ($response) {
            return $response->getErrorCodes();
        }

        return null;
    }

    /**
     * Retrieve the error messages from the last CAPTCHA verification.
     *
     * @return ?string[]
     */
    public function getLastErrorMessages(): ?iterable
    {
        $codes = $this->getLastErrorCodes();
        if ($codes) {
            return $this->getErrorMessages($codes);
        }

        return null;
    }

    /**
     * Retrieve the messages for the given error codes.
     *
     * @param  (string|int)[] $codes The error codes to resolve.
     * @return string[]
     */
    public function getErrorMessages(iterable $codes): iterable
    {
        $messages = [];
        foreach ($codes as $code) {
            $messages[$code] = $this->getErrorMessage($code);
        }

        return $messages;
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
                return 'The reCAPTCHA secret parameter is missing.';

            case 'invalid-input-secret':
                return 'The reCAPTCHA secret parameter is invalid or malformed.';

            case 'missing-input':
            case 'missing-input-response':
                return 'The CAPTCHA response parameter is missing.';

            case 'invalid-input':
            case 'invalid-input-response':
                return 'The CAPTCHA response parameter is invalid or malformed.';
        }

        return strtr('Unknown reCAPTCHA error: {code}', [
            '{code}' => $code,
        ]);
    }

    /**
     * Create a new ReCaptcha instance.
     *
     * @link   \ReCaptcha\ReCaptcha::__construct()
     * @throws UnexpectedValueException If the class is invalid.
     * @return ReCaptcha
     */
    protected function createClient(): ReCaptcha
    {
        $secret = $this->config->get('private_key');
        $class  = $this->getClientClass();

        try {
            $client = new $class($secret);
            $client = $this->configureClient($client);
        } catch (Throwable $t) {
            throw new UnexpectedValueException(sprintf(
                'Client class %s could not be prepared',
                $class
            ), 0, $t);
        }

        return $client;
    }

    /**
     * Configure a ReCaptcha instance.
     *
     * @param  ReCaptcha $client A ReCaptcha instance.
     * @return ReCaptcha
     */
    protected function configureClient(ReCaptcha $client): ReCaptcha
    {
        $configure = [
            'hostname'          => 'setExpectedHostname',
            'apk_package_name'  => 'setExpectedApkPackageName',
            'action'            => 'setExpectedAction',
            'score_threshold'   => 'setScoreThreshold',
            'challenge_timeout' => 'setChallengeTimeout',
        ];

        foreach ($configure as $key => $method) {
            if ($this->config->has($key)) {
                $client->{$method}($this->config->get($key));
            }
        }

        return $client;
    }

    /**
     * Retrieves the ReCaptcha client class.
     *
     * @return string
     */
    protected function getClientClass(): string
    {
        return $this->clientClass;
    }
}
