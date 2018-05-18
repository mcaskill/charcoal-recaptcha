# Google reCAPTCHA for Charcoal

[![License][badge-license]][charcoal/recaptcha]
[![Latest Stable Version][badge-version]][charcoal/recaptcha]
[![Code Quality][badge-scrutinizer]][dev-scrutinizer]
[![Coverage Status][badge-coveralls]][dev-coveralls]
[![Build Status][badge-travis]][dev-travis]

![Google reCAPTCHA for Charcoal](http://i.imgur.com/aHBOqAS.gif)

A [Charcoal][charcoal/app] service provider for the [Google reCAPTCHA client Library][google/recaptcha].

This package can be used as a PSR-7 middleware or as an object in your service layer.



## Installation

```shell
composer require mcaskill/charcoal-recaptcha
```

See [`composer.json`](composer.json) for depenencides.



## What's inside?

-   **`Charcoal\ReCaptcha\CaptchaServiceProvider`**
    for registering the CAPTCHA service, reCAPTCHA client, and configuration object.
-   **`Charcoal\ReCaptcha\CaptchaConfig`**
    for setting up the reCAPTCHA client.
-   **`Charcoal\ReCaptcha\CaptchaAwareTrait`**
    for convenience.
-   **`Charcoal\ReCaptcha\Captcha`**
    the service that handles the reCAPTCHA client.
-   **`Charcoal\ReCaptcha\LocalizedCaptcha`**
    a [translator-aware][charcoal/translator] variant of the service.



## Usage

```php
use ReCaptcha\ReCaptcha;
use Charcoal\ReCaptcha\Captcha;

$container[ReCaptcha::class] = new ReCaptcha($key);
$container[Captcha::class]   = new Captcha([
    'config' => [
        'public_key' => '…'
    ],
    'client' => $container[ReCaptcha::class]
]);

$captcha = $container[Captcha::class];

// As middleware
$app->post('/signup', '…')->add($captcha);

// As standalone, with direct user input
$captcha->verify($input, $ip);

// With a PSR-7 request
$captcha->verifyRequest($request);

// Display the widget in your views
echo $captcha->display(
    [
        'data-theme' => 'dark',
        'data-type' =>  'audio',
    ],
    [
        'hl' => 'fr'
    ]
);
```



## Service Provider

If [`CaptchaServiceProvider`](src/CaptchaServiceProvider.php) is used, the following are provided.



### Parameters

-   **charcoal/captcha/config**: An instance of [`CaptchaConfig`](src/CaptchaConfig.php).



### Services

-   **charcoal/captcha**: An instance of [`Captcha`](src/CaptchaConfig.php).
-   **google/recaptcha**: An instance of Google's [`ReCaptcha`][class-recaptcha].



### Registering

Via Charcoal configuration file:

```json
{
    "apis": {
        "google": {
            "recaptcha": {
                "public_key": "…",
                "private_key": "…"
            }
        },
    },
    "service_providers": {
        "charcoal/re-captcha/captcha": {}
    }
}
```

Via PHP:

```php
$container->register(new Charcoal\ReCaptcha\CaptchaServiceProvider(), [
    'charcoal/captcha/config' => [
        'public_key'  => '…',
        'private_key' => '…'
    ]
]);
```



## Acknowledgements

This package is inspired by:

- [`geggleto/psr7-recaptcha`](https://github.com/geggleto/psr7-recaptcha)
- [`anhskohbo/no-captcha`](https://github.com/anhskohbo/no-captcha)
- [`buzz/laravel-google-captcha`](https://github.com/thinhbuzz/laravel-google-captcha)



## License

-   Charcoal reCAPTCHA component is licensed under the MIT license. See [LICENSE](LICENSE) for details.
-   Charcoal framework is licensed under the MIT license. See [LICENSE][license-charcoal] for details.
-   Google reCAPTCHA PHP client library is licensed under the BSD License. See the [LICENSE][license-recaptcha] file for details.



[dev-scrutinizer]:     https://scrutinizer-ci.com/g/mcaskill/charcoal-recaptcha/
[dev-coveralls]:       https://coveralls.io/r/mcaskill/charcoal-recaptcha
[dev-travis]:          https://travis-ci.org/mcaskill/charcoal-recaptcha

[badge-license]:       https://img.shields.io/packagist/l/mcaskill/charcoal-recaptcha.svg?style=flat-square
[badge-version]:       https://img.shields.io/packagist/v/mcaskill/charcoal-recaptcha.svg?style=flat-square
[badge-scrutinizer]:   https://img.shields.io/scrutinizer/g/mcaskill/charcoal-recaptcha.svg?style=flat-square
[badge-coveralls]:     https://img.shields.io/coveralls/mcaskill/charcoal-recaptcha.svg?style=flat-square
[badge-travis]:        https://img.shields.io/travis/mcaskill/charcoal-recaptcha.svg?style=flat-square

[charcoal/recaptcha]:  https://packagist.org/packages/mcaskill/charcoal-recaptcha
[charcoal/app]:        https://packagist.org/packages/locomotivemtl/charcoal-app
[charcoal/translator]: https://packagist.org/packages/locomotivemtl/charcoal-translator

[pimple]:              https://packagist.org/packages/pimple/pimple
[google/recaptcha]:    https://packagist.org/packages/google/recaptcha
[class-recaptcha]:     https://github.com/google/recaptcha/blob/1.1.3/src/ReCaptcha/ReCaptcha.php

[license-charcoal]:    https://github.com/locomotivemtl/charcoal-app/blob/master/LICENSE
[license-recaptcha]:   https://github.com/google/recaptcha/blob/master/LICENSE
