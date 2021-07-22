<?php

namespace WebSK\Captcha;

use Slim\App;
use WebSK\Captcha\RequestHandlers\CheckCaptchaAjaxHandler;
use WebSK\Captcha\RequestHandlers\RenderCaptchaHandler;
use WebSK\Utils\HTTP;

/**
 * Class CaptchaRoutes
 * @package WebSK\Captcha
 */
class CaptchaRoutes
{
    const ROUTE_NAME_CAPTCHA_GENERATE = 'captcha:generate';
    const ROUTE_NAME_CAPTCHA_CHECK_AJAX = 'captcha:check_ajax';

    /**
     * @param App $app
     */
    public static function register(App $app)
    {
        $app->group('/captcha', function (App $app) {
            $app->map(
                [HTTP::METHOD_GET],
                '/generate',
                RenderCaptchaHandler::class
            )->setName(self::ROUTE_NAME_CAPTCHA_GENERATE);

            $app->map(
                [HTTP::METHOD_GET],
                '/check_ajax',
                CheckCaptchaAjaxHandler::class
            )->setName(self::ROUTE_NAME_CAPTCHA_CHECK_AJAX);
        });
    }
}
