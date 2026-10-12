<?php

namespace WebSK\Captcha\RequestHandlers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WebSK\Captcha\Captcha;

/**
 * Class CheckCaptchaAjaxHandler
 * @package WebSK\Captcha\RequestHandlers
 */
class CheckCaptchaAjaxHandler
{
    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $content = Captcha::check($request) ? 'true' : 'false';

        $response = $response->withHeader('Content-Type', 'application/json');
        $response->getBody()->write($content);

        return $response;
    }
}
