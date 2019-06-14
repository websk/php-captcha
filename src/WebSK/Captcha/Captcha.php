<?php

namespace WebSK\Captcha;

use WebSK\Utils\Messages;

/**
 * Class Captcha
 * @package WebSK\Captcha
 */
class Captcha
{
    const CAPTCHA_FIELD_NAME = 'captcha';
    const CAPTCHA_COOKIE_NAME = 'kvaYtnctkHqzTxR2b3Mi';

    /**
     * Проверка введенного кода
     * @return bool
     */
    public static function check()
    {
        if ((array_key_exists(self::CAPTCHA_FIELD_NAME, $_POST))
            && ($_POST[self::CAPTCHA_FIELD_NAME] == $_COOKIE[self::CAPTCHA_COOKIE_NAME])
        ) {
            return true;
        }

        return false;
    }

    /**
     * Проверка с генерацией сообщения в случае ошибки
     * @return bool
     */
    public static function checkWithMessage()
    {
        if (self::check()) {
            return true;
        }

        Messages::setError('Код, изображенный на картинке введен неверно. Попробуйте еще раз.');

        return false;
    }

    /**
     * Генерация картинки
     */
    public static function render()
    {
        $width = 140; // Ширина изображения
        $height = 40; // Высота изображения
        $num_symbols = 5; // Количество символов, которые нужно набрать
        $font_size = 14;
        $path_fonts = __DIR__ . '/fonts/'; // Путь к шрифтам
        $numeric = true; // Только цифры

        $_COOKIE[self::CAPTCHA_FIELD_NAME] = '';

        $number_of_signs = intval(($width * $height) / 150);

        $code = [];

        $letters = ['1', '2', '3', '4', '5', '6', '7', '8', '9'];

        if ($numeric === false) {
            $letters = [
                'a',
                'b',
                'c',
                'd',
                'e',
                'f',
                'g',
                'h',
                'j',
                'k',
                'm',
                'n',
                'p',
                'q',
                'r',
                's',
                't',
                'u',
                'v',
                'w',
                'x',
                'y',
                'z',
                '2',
                '3',
                '4',
                '5',
                '6',
                '7',
                '8',
                '9'
            ];
        }

        $figures_arr = array('50', '70', '90', '110', '130', '150', '170', '190', '210');

        $src = imagecreatetruecolor($width, $height);

        $fon = imagecolorallocate($src, 255, 255, 255);
        imagefill($src, 0, 0, $fon);

        $fonts_arr = [
            $path_fonts . 'font1.ttf',
            $path_fonts . 'font2.ttf'
        ];

        for ($i = 0; $i < $number_of_signs; $i++) {
            $h = 1;
            $color = imagecolorallocatealpha($src, rand(200, 200), rand(200, 200), rand(200, 200), 100);
            $font = $fonts_arr[rand(0, sizeof($fonts_arr) - 1)];
            $letter = mb_strtolower($letters[rand(0, sizeof($letters) - 1)]);
            $size = rand($font_size - 1, $font_size + 1);
            $angle = rand(0, 60);
            if ($h == rand(1, 2)) {
                $angle = rand(360, 300);
            }

            imagettftext($src, $size, $angle, rand($width * 0.1, $width - 20), rand($height * 0.2, $height - 10), $color, $font, $letter);
        }

        for ($i = 0; $i < $num_symbols; $i++) {
            $h = 1; // Ориентир

            $color = imagecolorallocatealpha(
                $src,
                $figures_arr[rand(0, sizeof($figures_arr) - 1)],
                $figures_arr[rand(0, sizeof($figures_arr) - 1)],
                $figures_arr[rand(0, sizeof($figures_arr) - 1)],
                rand(10, 30)
            );
            $font = $fonts_arr[rand(0, sizeof($fonts_arr) - 1)];
            $letter = mb_strtolower($letters[rand(0, sizeof($letters) - 1)]);
            $size = rand($font_size * 2.1 - 1, $font_size * 2.1 + 1);
            $x = (empty($x)) ? $width * 0.08 : $x + ($width * 0.8) / $num_symbols + rand(0, $width * 0.01);
            $y = ($h == rand(1, 2)) ? (($height * 1.15 * 3) / 4) + rand(0, $height * 0.02) : (($height * 1.15 * 3) / 4) - rand(0, $height * 0.02);
            $angle = rand(5, 20);

            $code[] = $letter;
            if ($h == rand(1, 2)) {
                $angle = rand(355, 340);
            }
            imagettftext($src, $size, $angle, $x, $y, $color, $font, $letter);
        }

        $captcha = mb_strtolower(implode('', $code));

        setcookie(self::CAPTCHA_COOKIE_NAME, $captcha, 0, '/');

        imagepng($src);
        imagedestroy($src);

        return;
    }
}
