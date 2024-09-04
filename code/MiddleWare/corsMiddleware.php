<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

$corsMiddleware = function (Request $request, RequestHandler $handler) {
    $response = $handler->handle($request);
    $response = $response->withHeader('Access-Control-Allow-Origin', '*')
                         ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
                         ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization')
                         ->withHeader('Access-Control-Max-Age', '3600');
                         
    return $response;
};