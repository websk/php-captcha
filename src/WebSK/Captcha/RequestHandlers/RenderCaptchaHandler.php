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
        $buffer_level = ob_get_level();
        if (!ob_start()) {
            throw new \RuntimeException('Unable to start the CAPTCHA output buffer.');
        }

        try {
            Captcha::render();
            $content = ob_get_clean();
            if ($content === false) {
                throw new \RuntimeException('Unable to read the CAPTCHA output buffer.');
            }
        } catch (\Throwable $exception) {
            while (ob_get_level() > $buffer_level) {
                ob_end_clean();
            }
            throw $exception;
        }

        $response = $response->withHeader('Content-Type', 'image/png');
        $response->getBody()->write($content);

        return $response;
    }
}
