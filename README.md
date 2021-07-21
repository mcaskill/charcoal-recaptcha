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

-   **`Charcoal\ReCaptcha\CaptchaServiceProvider`**: 
    A Pimple service provider.
-   **`Charcoal\ReCaptcha\CaptchaConfig`**: 
    A configuration repository for the CAPTCHA service wrapper.
-   **`Charcoal\ReCaptcha\CaptchaAwareTrait`**: 
    Convenient trait for interfacing with the CAPTCHA service wrapper.
-   **`Charcoal\ReCaptcha\CaptchaInterface`**: 
    Contract defining the core features of the CAPTCHA service wrapper.
-   **`Charcoal\ReCaptcha\Captcha`**: 
    Service wrapper of the reCAPTCHA client.
-   **`Charcoal\ReCaptcha\LocalizedCaptcha`**: 
    A variant of the service wrapper that is [Translator-aware][charcoal/translator].
-   **`Charcoal\ReCaptcha\HtmlAwareCaptcha`**: 
    A decorator of the service wrapper providing basic support for the JS API and HTML element.
-   **`Charcoal\ReCaptcha\HttpAwareCaptcha`**: 
    A decorator of the service wrapper providing basic support for verifying challenges using a [PSR-7] HTTP Request.



## Usage

```php
use Charcoal\ReCaptcha\Captcha;
use Charcoal\ReCaptcha\HttpAwareCaptcha;
use Charcoal\ReCaptcha\HtmlAwareCaptcha;

$captcha = new Captcha([
    'public_key'  => '…',
    'private_key' => '…',
]);

// Customize verification
$captcha->getClient()
    ->setExpectedHostname('recaptcha-demo.appspot.com')
    ->setExpectedAction('homepage')
    ->setScoreThreshold(0.5);

// As standalone, with direct user input
$captcha->verify($input, $ip);

// Decorate with PSR-7 support
$captcha = new HttpAwareCaptcha($captcha);

// As middleware
$app->post('/signup', '…')->add($captcha);

// With a PSR-7 request
$captcha->verifyRequest($request);

// Decorate with HTML support
$captcha = new HtmlAwareCaptcha($captcha);

// Display the widget in your views
echo $captcha->display([
    'data-theme' => 'dark',
    'data-type' =>  'audio',
], [
    'hl' => 'fr',
]);
```

```php
use Charcoal\ReCaptcha\LocalizedCaptcha;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Translation\Loader\XliffFileLoader;

$settings = [
    'public_key'  => '…',
    'private_key' => '…',
];

$translator = new Translator('fr');
$translator->addLoader('xliff', new XliffFileLoader());
$translator->addResource('xliff', '…/charcoal-recaptcha/translations', 'fr', 'validators');

$captcha = new LocalizedCaptcha($translator, $settings);

$captcha->verify($input);
```

By default, the `Captcha` wrapper will defer the instantiation of [`ReCaptcha`][class-recaptcha] until the first verification request.



### Custom `ReCaptcha` class

The `ReCaptcha` class be swapped using the `client_class` option.

```php
use Charcoal\ReCaptcha\Captcha;
use MyApp\MyCustomReCaptcha;

$settings = [
    'public_key'  => '…',
    'private_key' => '…',
];

$captcha = new Captcha($settings, MyCustomReCaptcha::class);
```



### Custom `ReCaptcha` instance

An instance of the `ReCaptcha` class can be assigned using the `client` option.

```php
use Charcoal\ReCaptcha\Captcha;
use ReCaptcha\ReCaptcha;
use ReCaptcha\RequestMethod\CurlPost;

$settings = [
    'public_key'  => '…',
    'private_key' => '…',
];

$client = new ReCaptcha($settings['private_key'], new CurlPost());

$captcha = new Captcha($settings, $client);
```



## Service Provider

If [`CaptchaServiceProvider`](src/CaptchaServiceProvider.php) is used, the following are provided.



### Parameters

-   **charcoal/captcha/config**: An instance of [`CaptchaConfig`](src/CaptchaConfig.php).



### Services

-   **charcoal/captcha**: An instance of [`HtmlAwareCaptcha`](src/HtmlAwareCaptcha.php).
    If the the PSR-7 `ServerRequestInterface` class exists, it will also use
    [`HttoAwareCaptcha`](src/HttoAwareCaptcha.php). If a `translator` service
    is present, it will use [`LocalizedCaptcha`](src/LocalizedCaptcha.php).



### Registering

Via Charcoal configuration file:

```json
{
    "apis": {
        "google": {
            "recaptcha": {
                "public_key": "…",
                "private_key": "…",
                "action": "…",
                "hostname": "…",
                "apk_package_name": "…",
                "score_threshold": 0.6,
                "challenge_timeout": 90
            }
        },
    },
    "service_providers": {
        "charcoal/re-captcha/captcha": {}
    },
    "translator": {
        "paths": [
            "vendor/mcaskill/charcoal-recaptcha/translations/"
        ]
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

[PSR-7]:               https://packagist.org/packages/psr/http-message
[pimple]:              https://packagist.org/packages/pimple/pimple
[google/recaptcha]:    https://packagist.org/packages/google/recaptcha
[class-recaptcha]:     https://github.com/google/recaptcha/blob/1.2.4/src/ReCaptcha/ReCaptcha.php

[license-charcoal]:    https://github.com/locomotivemtl/charcoal-app/blob/master/LICENSE
[license-recaptcha]:   https://github.com/google/recaptcha/blob/master/LICENSE
