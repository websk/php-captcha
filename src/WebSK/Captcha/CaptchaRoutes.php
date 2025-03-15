<?php

namespace WebSK\Captcha;

use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface;
use WebSK\Captcha\RequestHandlers\CheckCaptchaAjaxHandler;
use WebSK\Captcha\RequestHandlers\RenderCaptchaHandler;
use WebSK\Utils\HTTP;

/**
 * Class CaptchaRoutes
 * @package WebSK\Captcha
 */
class CaptchaRoutes
{
    const string ROUTE_NAME_CAPTCHA_GENERATE = 'captcha:generate';
    const string ROUTE_NAME_CAPTCHA_CHECK_AJAX = 'captcha:check_ajax';

    /**
     * @param App $app
     */
    public static function register(App $app): void
    {
        $app->group('/captcha', function (RouteCollectorProxyInterface $route_collector_proxy) {
            $route_collector_proxy->map(
                [HTTP::METHOD_GET],
                '/generate',
                RenderCaptchaHandler::class
            )->setName(self::ROUTE_NAME_CAPTCHA_GENERATE);

            $route_collector_proxy->map(
                [HTTP::METHOD_GET],
                '/check_ajax',
                CheckCaptchaAjaxHandler::class
            )->setName(self::ROUTE_NAME_CAPTCHA_CHECK_AJAX);
        });
    }
}
