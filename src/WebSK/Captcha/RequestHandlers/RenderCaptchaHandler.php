<?php

namespace WebSK\Captcha\RequestHandlers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WebSK\Captcha\Captcha;

/**
 * Class RenderCaptchaHandler
 * @package WebSK\Captcha\RequestHandlers
 */
class RenderCaptchaHandler
{
    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        ob_start();
        Captcha::render();
        $content = ob_get_clean();

        $response = $response->withHeader('Content-type', 'image/png');
        $response->getBody()->write($content);

        return $response;
    }
}
