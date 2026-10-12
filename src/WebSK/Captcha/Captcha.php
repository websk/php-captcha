<?php

namespace WebSK\Captcha;

use GdImage;
use InvalidArgumentException;
use Psr\Http\Message\ServerRequestInterface;
use RuntimeException;
use Stringable;
use WebSK\Slim\Request;
use WebSK\Utils\Messages;

/**
 * Class Captcha
 * @package WebSK\Captcha
 */
class Captcha
{
    const string CAPTCHA_FIELD_NAME = 'captcha';
    const string CAPTCHA_COOKIE_NAME = 'kvaYtnctkHqzTxR2b3Mi';
    private const string CAPTCHA_SESSION_KEY = 'websk_captcha_code';

    /**
     * Проверка введенного кода
     * @return bool
     */
    public static function check(?ServerRequestInterface $request = null): bool
    {
        if ($request === null) {
            $facade_request = Request::self();
            if (!$facade_request instanceof ServerRequestInterface) {
                throw new RuntimeException('The request facade must contain a PSR-7 server request.');
            }
            $request = $facade_request;
        }

        $captcha_from_request = self::getCaptchaFromRequest($request);
        if ($captcha_from_request === null || $captcha_from_request === '') {
            return false;
        }

        self::startSession();

        $expected_captcha = $_SESSION[self::CAPTCHA_SESSION_KEY] ?? null;
        unset($_SESSION[self::CAPTCHA_SESSION_KEY]);

        if (!is_string($expected_captcha) || $expected_captcha === '') {
            return false;
        }

        return hash_equals($expected_captcha, mb_strtolower($captcha_from_request));
    }

    /**
     * Проверка с генерацией сообщения в случае ошибки
     * @return bool
     */
    public static function checkWithMessage(?ServerRequestInterface $request = null): bool
    {
        if (self::check($request)) {
            return true;
        }

        Messages::setError('Код, изображенный на картинке введен неверно. Попробуйте еще раз.');

        return false;
    }

    /**
     * Генерация картинки
     */
    public static function render(): void
    {
        $width = 140; // Ширина изображения
        $height = 40; // Высота изображения
        $num_symbols = 5; // Количество символов, которые нужно набрать
        $font_size = 14;
        $path_fonts = __DIR__ . '/fonts/'; // Путь к шрифтам

        self::startSession();

        $number_of_signs = intval(($width * $height) / 150);

        $code = [];

        $letters = ['1', '2', '3', '4', '5', '6', '7', '8', '9'];

        $figures_arr = [50, 70, 90, 110, 130, 150, 170, 190, 210];

        $src = imagecreatetruecolor($width, $height);
        if (!$src instanceof GdImage) {
            throw new RuntimeException('Unable to create the CAPTCHA image.');
        }

        $fon = imagecolorallocate($src, 255, 255, 255);
        if ($fon === false) {
            throw new RuntimeException('Unable to initialize the CAPTCHA background.');
        }
        imagefill($src, 0, 0, $fon);

        $fonts_arr = [
            $path_fonts . 'font1.ttf',
            $path_fonts . 'font2.ttf'
        ];

        foreach ($fonts_arr as $font) {
            if (!is_file($font) || !is_readable($font)) {
                throw new RuntimeException('CAPTCHA font is missing or unreadable: ' . $font);
            }
        }

        for ($i = 0; $i < $number_of_signs; $i++) {
            $h = 1;
            $color = imagecolorallocatealpha($src, 200, 200, 200, 100);
            if ($color === false) {
                throw new RuntimeException('Unable to allocate a CAPTCHA noise color.');
            }
            $font = $fonts_arr[random_int(0, count($fonts_arr) - 1)];
            $letter = $letters[random_int(0, count($letters) - 1)];
            $size = random_int($font_size - 1, $font_size + 1);
            $angle = random_int(0, 60);
            if ($h === random_int(1, 2)) {
                $angle = random_int(300, 360);
            }

            $text_box = imagettftext(
                $src,
                $size,
                $angle,
                intval(static::random_float($width * 0.1, $width - 20)),
                intval(static::random_float($height * 0.2, $height - 10)),
                $color,
                $font,
                $letter
            );
            if ($text_box === false) {
                throw new RuntimeException('Unable to render CAPTCHA noise.');
            }
        }

        $x = null;
        for ($i = 0; $i < $num_symbols; $i++) {
            $h = 1; // Ориентир

            $color = imagecolorallocatealpha(
                $src,
                $figures_arr[random_int(0, count($figures_arr) - 1)],
                $figures_arr[random_int(0, count($figures_arr) - 1)],
                $figures_arr[random_int(0, count($figures_arr) - 1)],
                random_int(10, 30)
            );
            if ($color === false) {
                throw new RuntimeException('Unable to allocate a CAPTCHA text color.');
            }
            $font = $fonts_arr[random_int(0, count($fonts_arr) - 1)];
            $letter = $letters[random_int(0, count($letters) - 1)];
            $size = static::random_float($font_size * 2.1 - 1, $font_size * 2.1 + 1);
            $x = ($x === null) ? $width * 0.08 : $x + ($width * 0.8) / $num_symbols + static::random_float(0, $width * 0.01);
            $y = ($h === random_int(1, 2)) ? (($height * 1.15 * 3) / 4) + static::random_float(0, $height * 0.02) : (($height * 1.15 * 3) / 4) - static::random_float(0, $height * 0.02);
            $angle = random_int(5, 20);

            $code[] = $letter;
            if ($h === random_int(1, 2)) {
                $angle = random_int(340, 355);
            }
            $text_box = imagettftext($src, $size, $angle, (int)$x, (int)$y, $color, $font, $letter);
            if ($text_box === false) {
                throw new RuntimeException('Unable to render CAPTCHA text.');
            }
        }

        $captcha = implode('', $code);

        $_SESSION[self::CAPTCHA_SESSION_KEY] = $captcha;

        if (!imagepng($src)) {
            throw new RuntimeException('Unable to encode the CAPTCHA image as PNG.');
        }
    }

    public static function random_float(float $min, float $max): float
    {
        if ($max < $min) {
            throw new InvalidArgumentException('The maximum must be greater than or equal to the minimum.');
        }

        if ($max === $min) {
            return $min;
        }

        return $min + (random_int(0, PHP_INT_MAX) / PHP_INT_MAX) * ($max - $min);
    }

    private static function getCaptchaFromRequest(ServerRequestInterface $request): ?string
    {
        $value = null;
        $parsed_body = $request->getParsedBody();

        if (is_array($parsed_body) && array_key_exists(self::CAPTCHA_FIELD_NAME, $parsed_body)) {
            $value = $parsed_body[self::CAPTCHA_FIELD_NAME];
        } elseif (is_object($parsed_body) && property_exists($parsed_body, self::CAPTCHA_FIELD_NAME)) {
            $value = $parsed_body->{self::CAPTCHA_FIELD_NAME};
        } else {
            $query_params = $request->getQueryParams();
            $value = $query_params[self::CAPTCHA_FIELD_NAME] ?? null;
        }

        if (is_string($value) || is_int($value) || $value instanceof Stringable) {
            return (string)$value;
        }

        return null;
    }

    private static function startSession(): void
    {
        if (session_status() === PHP_SESSION_DISABLED) {
            throw new RuntimeException('PHP sessions are required to use CAPTCHA.');
        }

        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        $cookie_params = session_get_cookie_params();
        $configured_same_site = ini_get('session.cookie_samesite');
        $same_site = is_string($configured_same_site)
            && in_array(strtolower($configured_same_site), ['lax', 'strict', 'none'], true)
                ? $configured_same_site
                : 'Lax';
        $https = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== '' && $_SERVER['HTTPS'] !== 'off';
        if (!session_start([
            'use_strict_mode' => 1,
            'cookie_httponly' => 1,
            'cookie_samesite' => $same_site,
            'cookie_secure' => $cookie_params['secure'] || $https,
        ])) {
            throw new RuntimeException('Unable to start the CAPTCHA session.');
        }
    }
}
