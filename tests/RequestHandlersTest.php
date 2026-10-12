<?php

declare(strict_types=1);

namespace WebSK\Captcha\Tests;

use PHPUnit\Framework\TestCase;
use Slim\Factory\AppFactory;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Psr7\Factory\ServerRequestFactory;
use WebSK\Captcha\Captcha;
use WebSK\Captcha\CaptchaRoutes;
use WebSK\Captcha\RequestHandlers\CheckCaptchaAjaxHandler;
use WebSK\Captcha\RequestHandlers\RenderCaptchaHandler;

final class RequestHandlersTest extends TestCase
{
    private const string SESSION_KEY = 'websk_captcha_code';

    protected function setUp(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION = [];
        }
    }

    public static function tearDownAfterClass(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION = [];
            session_write_close();
        }
    }

    public function testRenderHandlerReturnsPngResponse(): void
    {
        $request = (new ServerRequestFactory())->createServerRequest('GET', '/captcha/generate');
        $response = (new RenderCaptchaHandler())($request, (new ResponseFactory())->createResponse());

        self::assertSame('image/png', $response->getHeaderLine('Content-Type'));
        self::assertStringStartsWith("\x89PNG\r\n\x1a\n", (string)$response->getBody());
    }

    public function testCheckHandlerReturnsJsonBoolean(): void
    {
        $expected = $this->renderAndGetExpectedCode();
        $request = (new ServerRequestFactory())
            ->createServerRequest('GET', '/captcha/check_ajax')
            ->withQueryParams([Captcha::CAPTCHA_FIELD_NAME => $expected]);
        $response = (new CheckCaptchaAjaxHandler())($request, (new ResponseFactory())->createResponse());

        self::assertSame('application/json', $response->getHeaderLine('Content-Type'));
        self::assertSame('true', (string)$response->getBody());
    }

    public function testRoutesAreRegisteredAndRunWithoutFacadeInitialization(): void
    {
        $app = AppFactory::create();
        CaptchaRoutes::register($app);

        $generate_route = $app->getRouteCollector()->getNamedRoute(CaptchaRoutes::ROUTE_NAME_CAPTCHA_GENERATE);
        $check_route = $app->getRouteCollector()->getNamedRoute(CaptchaRoutes::ROUTE_NAME_CAPTCHA_CHECK_AJAX);
        self::assertSame('/captcha/generate', $generate_route->getPattern());
        self::assertSame(['GET'], $generate_route->getMethods());
        self::assertSame('/captcha/check_ajax', $check_route->getPattern());
        self::assertSame(['GET'], $check_route->getMethods());

        $generate_request = (new ServerRequestFactory())->createServerRequest('GET', '/captcha/generate');
        $generate_response = $app->handle($generate_request);
        self::assertSame(200, $generate_response->getStatusCode());
        self::assertSame('image/png', $generate_response->getHeaderLine('Content-Type'));

        $expected = $_SESSION[self::SESSION_KEY] ?? null;
        self::assertIsString($expected);

        $check_request = (new ServerRequestFactory())
            ->createServerRequest('GET', '/captcha/check_ajax')
            ->withQueryParams([Captcha::CAPTCHA_FIELD_NAME => $expected]);
        $check_response = $app->handle($check_request);
        self::assertSame(200, $check_response->getStatusCode());
        self::assertSame('true', (string)$check_response->getBody());
    }

    private function renderAndGetExpectedCode(): string
    {
        ob_start();
        Captcha::render();
        ob_end_clean();

        $expected = $_SESSION[self::SESSION_KEY] ?? null;
        self::assertIsString($expected);

        return $expected;
    }
}
