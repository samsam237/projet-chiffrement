<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

$staticRouteMiddleware = function (Request $request, RequestHandler $handler) {
    $uri = $request->getUri()->getPath();
    $file = __DIR__ . '/../public' . $uri;

    if (is_file($file)) {
        return new \Slim\Psr7\Response(
            file_get_contents($file),
            200,
            ['Content-Type' => mime_content_type($file)]
        );
        $response = new \Slim\Psr7\Response();
        $body = $response->getBody();
        $body->write(file_get_contents($file));
        return $response->withHeader('Content-Type', mime_content_type($file))->withStatus(200);
    }

    return $handler->handle($request);
};