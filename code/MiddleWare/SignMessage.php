<?php


use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

require __DIR__ . '/../src/RSAEncryption.php';

$signResponse = function (Request $request, RequestHandler $handler) {
    
    $response = $handler->handle($request);
    $value = $response->getBody()->getContents();
    
    $body = json_decode($response->getBody());

    $body['signature'] = 

    // Set the modified body back to the response
    $response->getBody()->write($body);

    // Add a custom header
    $response = $response->withHeader('X-Custom-Header', 'Value');

    return $response;

    
    $response = $response->withHeader('X-Added-Header', 'some-value');
    
    return $response;
};