# WebSK PHP Captcha

`websk/php-captcha` generates PNG CAPTCHA images and provides Slim 4 route
handlers for generating and validating them.

## Requirements

- PHP 8.3 or later, including PHP 8.5
- GD with FreeType support
- Mbstring
- PHP sessions
- Slim 4

## Installation

Install the package with Composer:

```shell
composer require websk/php-captcha
```

The package is available on
[Packagist](https://packagist.org/packages/websk/php-captcha).

## Slim integration

Register the CAPTCHA routes on an existing Slim application:

```php
<?php

use Slim\Factory\AppFactory;
use WebSK\Captcha\CaptchaRoutes;

require __DIR__ . '/vendor/autoload.php';

$app = AppFactory::create();

CaptchaRoutes::register($app);

$app->run();
```

This adds two named `GET` routes:

| Route | Name | Response |
| --- | --- | --- |
| `/captcha/generate` | `captcha:generate` | `image/png` |
| `/captcha/check_ajax` | `captcha:check_ajax` | JSON `true` or `false` |

Display the generated image and use the `captcha` field for the entered code:

```html
<img src="/captcha/generate" alt="CAPTCHA">
<input type="text" name="captcha" inputmode="numeric" autocomplete="off">
```

The validation route accepts the code from parsed request data or the query
string:

```text
GET /captcha/check_ajax?captcha=12345
```

The expected value is stored in the server-side PHP session and is removed
after a non-empty validation attempt. Generate a new image before retrying.
When no session is active, the package starts one with strict mode, an
`HttpOnly` cookie, and `SameSite=Lax` by default. Existing session settings are
used when the application starts the session first.

Request handlers pass their PSR-7 request directly to `Captcha::check()`. Code
outside a handler can do the same:

```php
$valid = \WebSK\Captcha\Captcha::check($request);
```

## Demo

Run the bundled Slim application:

```shell
composer install
cd public
php -S localhost:8000
```

Then open <http://localhost:8000>. The root route redirects to the generated
CAPTCHA image.

## Development

Run syntax checks, PHPUnit, and PHPStan together:

```shell
composer check
```

The individual commands are also available:

```shell
composer lint
composer test
composer phpstan
```

## Upgrade from 2.x

Version 3.0 stores the expected CAPTCHA value in the server-side session. The
2.x client-side CAPTCHA cookie is no longer trusted. Applications must have PHP
sessions available and should pass the current PSR-7 request to
`Captcha::check()`.

## License

This project is licensed under the [MIT License](LICENSE).
