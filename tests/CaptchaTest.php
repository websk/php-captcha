<?php

declare(strict_types=1);

namespace WebSK\Captcha\Tests;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Slim\Psr7\Factory\ServerRequestFactory;
use WebSK\Captcha\Captcha;

final class CaptchaTest extends TestCase
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

    public function testRenderProducesPngAndStoresExpectedCodeInSession(): void
    {
        ob_start();
        Captcha::render();
        $png = ob_get_clean();

        self::assertIsString($png);
        self::assertStringStartsWith("\x89PNG\r\n\x1a\n", $png);

        $image_info = getimagesizefromstring($png);
        self::assertIsArray($image_info);
        self::assertSame(140, $image_info[0]);
        self::assertSame(40, $image_info[1]);
        self::assertSame('image/png', $image_info['mime']);

        self::assertArrayHasKey(self::SESSION_KEY, $_SESSION);
        $expected = $_SESSION[self::SESSION_KEY];
        self::assertIsString($expected);
        self::assertMatchesRegularExpression('/^[1-9]{5}$/', $expected);
    }

    public function testCheckAcceptsMatchingQueryValueOnlyOnce(): void
    {
        $expected = $this->renderAndGetExpectedCode();
        $request = (new ServerRequestFactory())
            ->createServerRequest('GET', '/captcha/check_ajax')
            ->withQueryParams([Captcha::CAPTCHA_FIELD_NAME => $expected]);

        self::assertTrue(Captcha::check($request));
        self::assertFalse(Captcha::check($request));
    }

    public function testCheckReadsParsedBodyBeforeQueryParameters(): void
    {
        $expected = $this->renderAndGetExpectedCode();
        $request = (new ServerRequestFactory())
            ->createServerRequest('POST', '/captcha/check_ajax')
            ->withParsedBody([Captcha::CAPTCHA_FIELD_NAME => $expected])
            ->withQueryParams([Captcha::CAPTCHA_FIELD_NAME => '11111']);

        self::assertTrue(Captcha::check($request));
    }

    public function testCheckReadsObjectParsedBody(): void
    {
        $expected = $this->renderAndGetExpectedCode();
        $request = (new ServerRequestFactory())
            ->createServerRequest('POST', '/captcha/check_ajax')
            ->withParsedBody((object)[Captcha::CAPTCHA_FIELD_NAME => $expected]);

        self::assertTrue(Captcha::check($request));
    }

    public function testCheckRejectsMissingInvalidAndNonScalarValues(): void
    {
        $factory = new ServerRequestFactory();

        self::assertFalse(Captcha::check($factory->createServerRequest('GET', '/captcha/check_ajax')));

        $expected = $this->renderAndGetExpectedCode();
        $empty_request = $factory
            ->createServerRequest('GET', '/captcha/check_ajax')
            ->withQueryParams([Captcha::CAPTCHA_FIELD_NAME => '']);
        self::assertFalse(Captcha::check($empty_request));

        $valid_request = $factory
            ->createServerRequest('GET', '/captcha/check_ajax')
            ->withQueryParams([Captcha::CAPTCHA_FIELD_NAME => $expected]);
        self::assertTrue(Captcha::check($valid_request));

        $this->renderAndGetExpectedCode();
        $array_request = $factory
            ->createServerRequest('GET', '/captcha/check_ajax')
            ->withQueryParams([Captcha::CAPTCHA_FIELD_NAME => ['11111']]);
        self::assertFalse(Captcha::check($array_request));

        $this->renderAndGetExpectedCode();
        $wrong_request = $factory
            ->createServerRequest('GET', '/captcha/check_ajax')
            ->withQueryParams([Captcha::CAPTCHA_FIELD_NAME => '11111']);
        self::assertFalse(Captcha::check($wrong_request));
    }

    public function testClientCookieCannotSupplyExpectedCaptcha(): void
    {
        $request = (new ServerRequestFactory())
            ->createServerRequest('GET', '/captcha/check_ajax')
            ->withQueryParams([Captcha::CAPTCHA_FIELD_NAME => '12345'])
            ->withCookieParams([Captcha::CAPTCHA_COOKIE_NAME => '12345']);

        self::assertFalse(Captcha::check($request));
    }

    public function testRandomFloatStaysWithinRequestedRange(): void
    {
        for ($i = 0; $i < 20; $i++) {
            $value = Captcha::random_float(1.5, 2.5);
            self::assertGreaterThanOrEqual(1.5, $value);
            self::assertLessThanOrEqual(2.5, $value);
        }

        self::assertSame(2.0, Captcha::random_float(2.0, 2.0));
    }

    public function testRandomFloatRejectsReversedRange(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Captcha::random_float(2.0, 1.0);
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
