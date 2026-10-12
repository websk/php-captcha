<?php

use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Factory\AppFactory;
use WebSK\Captcha\CaptchaRoutes;

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();

$app->get('/', function (ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {
    return $response->withHeader('Location', '/captcha/generate')
        ->withStatus(StatusCodeInterface::STATUS_FOUND);
});

CaptchaRoutes::register($app);

$app->run();
