# Charcoal reCAPTCHA

Google reCAPTCHA validator for [Charcoal][charcoal-core] projects.

## Requirements

| Prerequisite    | How to check  | How to install |
| --------------- | ------------- | -------------- |
| PHP >= 5.6.x    | `php -v`      | [php.net](//php.net/manual/en/install.php)
| Composer 1.0.0  | `composer -v` | [getcomposer.org](//getcomposer.org/)
| Charcoal        |               | [charcoal-project-boilerplate][charcoal-project]

See [composer.json](blob/master/composer.json) for depenencides.

## Installation

```shell
composer require mcaskill/charcoal-recaptcha
```

## What's inside?

-   **`Charcoal\ReCaptcha\CaptchaServiceProvider`**
    for registering the reCAPTCHA service and configuration object.
-   **`Charcoal\ReCaptcha\CaptchaConfig`**
    for setting up the reCAPTCHA service.
-   **`Charcoal\ReCaptcha\CaptchaAwareTrait`**
    for providing validation for entities (e.g., Actions).

## Provides

### Parameters

-   **google/recaptcha/config**: An instance of [`CaptchaConfig`](blob/master/src/CaptchaConfig.php), stores the service's settings.
-   **google/recaptcha/public_key**: The site key used for displaying the widget.
-   **google/recaptcha/private_key**: The secret key shared between your application backend and the reCAPTCHA server to verify the user's response.

### Services

-   **google/recaptcha**: An instance of [`ReCaptcha`][recaptcha-class], that is used for verifying the user's response.

### Registering

Via Charcoal configuration file:

```json
{
    "google": {
        "recaptcha": {
            "public_key": "…",
            "private_key": "…"
        }
    },
    "service_providers": {
        "charcoal/recaptcha/captcha": {}
    }
}
```

Via PHP:

```php
$container->register(new Charcoal\ReCaptcha\CaptchaServiceProvider(), [
    'google/recaptcha/public_key'  => '…',
    'google/recaptcha/private_key' => '…'
]);
```

[charcoal-core]: https://github.com/locomotivemtl/charcoal-core
[charcoal-project]: https://github.com/locomotivemtl/charcoal-project-boilerplate
[recaptcha-class]: https://github.com/google/recaptcha/blob/master/src/ReCaptcha/ReCaptcha.php
