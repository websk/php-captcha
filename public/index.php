<?php

use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Factory\AppFactory;
use WebSK\Captcha\CaptchaRoutes;

require '../vendor/autoload.php';

$app = AppFactory::create();

$container = $app->getContainer();

$app->get('/', function (ServerRequestInterface $request, ResponseInterface $response, $args) {
    return $response->withHeader('Location', '/captcha/generate')
        ->withStatus(StatusCodeInterface::STATUS_FOUND);
});

CaptchaRoutes::register($app);

$app->run();