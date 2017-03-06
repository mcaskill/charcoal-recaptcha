# Google reCAPTCHA for [Charcoal][charcoal-app]

![google captcha for laravel 5](http://i.imgur.com/aHBOqAS.gif)

This package can be used as a PSR-7 middleware or as an object in your service layer.

## Installation

```shell
composer require mcaskill/charcoal-recaptcha
```

See [composer.json](composer.json) for depenencides.

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
    a [translator-aware](charcoal-translator) variant of the service.

## Usage

```php
use \ReCaptcha\ReCaptcha;
use \Charcoal\ReCaptcha\Captcha;

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

If [`CaptchaServiceProvider`] is used, the following are provided.

### Parameters

-   **charcoal/captcha/config**: An instance of [`CaptchaConfig`](src/CaptchaConfig.php).
-   **google/recaptcha/public_key**: The site key used for displaying the widget.
-   **google/recaptcha/private_key**: The secret key shared between your application backend and the reCAPTCHA server to verify the user's response.

### Services

-   **charcoal/captcha**: An instance of [`Captcha`](src/CaptchaConfig.php).
-   **google/recaptcha**: An instance of Google's [`ReCaptcha`][recaptcha-class].

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

[charcoal-app]: https://github.com/locomotivemtl/charcoal-app
[charcoal-translator]: https://github.com/locomotivemtl/charcoal-translator
[recaptcha]: https://github.com/google/recaptcha
[recaptcha-class]: https://github.com/google/recaptcha/blob/master/src/ReCaptcha/ReCaptcha.php
