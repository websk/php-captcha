# Changelog

All notable changes to this project are documented in this file.

## 3.0.0 - 2026-10-12

### Added

- PHP 8.5 compatibility verification.
- Server-side, single-use CAPTCHA storage through PHP sessions.
- PHPUnit 12.5 tests and PHPStan 2.x analysis at level `max`.
- Strict Composer validation, platform, lint, test, and analysis scripts.
- Explicit MIT licensing and PSR-4 namespace metadata.

### Changed

- Updated Slim to 4.15, `websk/php-slim` to 2.4, and `websk/php-utils` to 3.0.
- Passed PSR-7 requests directly from the validation handler.
- Replaced predictable random selection with `random_int()`.
- Hardened GD image generation, output buffering, and session-cookie defaults.

### Removed

- Client-controlled CAPTCHA answer storage.
- The deprecated PHP 8.5 `imagedestroy()` call.

### Compatibility

- Requires PHP 8.3 or later with GD, Mbstring, and Session extensions.
- Moving CAPTCHA state from a client cookie to a server session is a breaking
  behavioral change and is the reason for the 3.0 major version.

## [2.3.2] - 2026-05-31

### Added

- Added a runnable Slim demo application and demo instructions.
- Added `random_float()` for coordinates and font sizes that require fractional
  ranges.

### Fixed

- Corrected reversed random-number bounds and converted GD coordinates to
  integers.
- Added the `void` return type to CAPTCHA rendering and corrected the request
  interface annotation.
- Refreshed locked dependencies, including `websk/php-slim` 2.3.2.

## [2.3.1] - 2025-03-29

### Fixed

- Replaced the removed `WebSK\Utils\HTTP` method constant with the standard
  `Fig\Http\Message\RequestMethodInterface::METHOD_GET` constant.

## [2.3.0] - 2025-03-26

### Changed

- Updated `websk/php-slim` from 2.2 to 2.3.
- Updated the locked Slim HTTP stack to `slim/http` 1.4 and `slim/psr7` 1.7.

## [2.2.0] - 2025-03-25

### Changed

- Updated `websk/php-utils` from 2.0 to 2.1.

## [2.1.1] - 2025-03-15

### Changed

- Updated grouped-route registration to use Slim's
  `RouteCollectorProxyInterface`.
- Expanded the README with Composer installation instructions.

## [2.1.0] - 2025-02-09

### Changed

- Added an explicit Slim 4.14 dependency and updated `websk/php-slim` to 2.2.
- Declared `CaptchaRoutes::register()` as returning `void`.

## [2.0.0] - 2024-08-22

### Changed

- Raised the minimum PHP version from 7.4 to 8.3.
- Updated `websk/php-slim` and `websk/php-utils` to their 2.x lines.
- Added native string types to public class constants.

## [1.1.0] - 2022-02-13

### Changed

- Updated `websk/php-slim` from 1.0 to 1.1.
- Removed the unused alphanumeric CAPTCHA branch and retained numeric CAPTCHA
  generation.

## [1.0.6] - 2021-07-22

### Added

- Declared PHP 7.4 as the minimum supported version.
- Added scalar return types to CAPTCHA checks.

### Changed

- Updated request handlers to use PSR-7 server-request and response interfaces.

## [1.0.5] - 2019-10-25

### Fixed

- Read the submitted CAPTCHA through the WebSK request facade instead of
  accessing `$_POST` directly.

## [1.0.4] - 2019-06-14

### Changed

- Limited CAPTCHA input lookup to POST data instead of the broader `$_REQUEST`
  superglobal.

## [1.0.3] - 2019-06-14

### Changed

- Moved the expected value to a dedicated opaque cookie name instead of the
  public form-field name.

## [1.0.2] - 2019-06-14

### Added

- Declared the GD and Mbstring extension requirements.

### Changed

- Moved CAPTCHA answer storage from the PHP session to a client cookie.

## [1.0.1] - 2019-02-27

### Added

- Added the `websk/php-utils` dependency used for validation error messages.

## [1.0.0] - 2019-02-19

### Added

- Initial CAPTCHA image generator with bundled TrueType fonts.
- Slim routes and request handlers for image generation and validation.
- Session-backed CAPTCHA validation and WebSK flash error messages.

[2.3.2]: https://github.com/websk/php-captcha/releases/tag/2.3.2
[2.3.1]: https://github.com/websk/php-captcha/releases/tag/2.3.1
[2.3.0]: https://github.com/websk/php-captcha/releases/tag/2.3.0
[2.2.0]: https://github.com/websk/php-captcha/releases/tag/2.2.0
[2.1.1]: https://github.com/websk/php-captcha/releases/tag/2.1.1
[2.1.0]: https://github.com/websk/php-captcha/releases/tag/2.1.0
[2.0.0]: https://github.com/websk/php-captcha/releases/tag/2.0.0
[1.1.0]: https://github.com/websk/php-captcha/releases/tag/1.1.0
[1.0.6]: https://github.com/websk/php-captcha/releases/tag/1.0.6
[1.0.5]: https://github.com/websk/php-captcha/releases/tag/1.0.5
[1.0.4]: https://github.com/websk/php-captcha/releases/tag/1.0.4
[1.0.3]: https://github.com/websk/php-captcha/releases/tag/1.0.3
[1.0.2]: https://github.com/websk/php-captcha/releases/tag/1.0.2
[1.0.1]: https://github.com/websk/php-captcha/releases/tag/1.0.1
[1.0.0]: https://github.com/websk/php-captcha/releases/tag/1.0.0
