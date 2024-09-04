<?php
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Factory\AppFactory;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;

require __DIR__ . '/../src/RSAEncryption.php';

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../MiddleWare/BodyParser.php';
/* require __DIR__ . '/../MiddleWare/StaticRoute.php';
require __DIR__ . '/../MiddleWare/CORS.php';
 */
//$rsa_algorithm = new RSAEncryption(4096, __DIR__.'/openssl.cnf');

$rsa_algorithm = new RSAEncryption(4096, null, 'file://'.__DIR__.'\..\keys\key.pem', 'file://'.__DIR__.'\..\keys\public.pem');

$app = AppFactory::create();

$twig = Twig::create(__DIR__ . '/../public/templates', ['cache' => false]);

$app->add(TwigMiddleware::create($app, $twig));
//$app->add('corsMiddleware');

$signResponseMiddleware = function (Request $request, RequestHandler $handler) use ($rsa_algorithm) {
    
    $response = $handler->handle($request);
    $value = $response->getBody();
    
    $body = json_decode($value, true);
    
    $signature = ['sign' =>  $rsa_algorithm->signMessage(  strval($value) )];

    $signature = json_encode($signature);

    $response->getBody()->write($signature);
    
    return $response;
};

$app->add($signResponseMiddleware);

$app->get('/', function ($request, $response) {
    $view = Twig::fromRequest($request);
    
    return $view->render($response, 'index.html.twig');
});

$app->get('/getKeys', function ($request, $response) use ($rsa_algorithm) {
    $responseData = $rsa_algorithm->getKeysAsJson();
    
    $response->getBody()->write($responseData);
    return $response->withHeader('content-type', 'application/json');
});

$app->post('/encrypt', function (Request $request, Response $response, $args) use ($rsa_algorithm) {
    $data = $request->getParsedBody();

    $message = $data['message'];

    $responseData = ['message' => $rsa_algorithm->encryptWithPublicKey($message) ];
    
    $jsonResponse = json_encode($responseData);

    if ($jsonResponse === false) {
        $error = json_last_error_msg();
        $error_response = json_encode(["error" => "JSON encoding error: " . $error]);
        $response->getBody()->write($error_response);
        return $response->withStatus(500)->withHeader('content-type', 'application/json');
    }
    
    $response->getBody()->write($jsonResponse);
    return $response->withHeader('content-type', 'application/json');
})->add($jsonBodyParser);

$app->post('/decrypt', function (Request $request, Response $response, $args) use ($rsa_algorithm) {
    $data = $request->getParsedBody();
    
    $message = $data['message'];

    // Create a response
    $responseData = ['message' =>  $rsa_algorithm->decryptWithPrivateKey(  $message )];
    // Set response body as JSON
    $jsonResponse = json_encode($responseData );

    if ($jsonResponse === false) {
        $error = json_last_error_msg();
        $error_response = json_encode(["error" => "JSON encoding error: " . $error]);
        $response->getBody()->write($error_response);
        return $response->withStatus(500)->withHeader('content-type', 'application/json');
    }

    $response->getBody()->write($jsonResponse);
    return $response->withHeader('Content-Type', 'application/json');
})->add($jsonBodyParser);

$app->post('/sign', function (Request $request, Response $response, $args) use ($rsa_algorithm) {
    $data = $request->getParsedBody();
    
    $message = $data['message'];

    // Create a response
    $responseData = ['sign' =>  $rsa_algorithm->signMessage(  $message )];
    // Set response body as JSON
    $jsonResponse = json_encode($responseData );

    if ($jsonResponse === false) {
        $error = json_last_error_msg();
        $error_response = json_encode(["error" => "JSON encoding error: " . $error]);
        $response->getBody()->write($error_response);
        return $response->withStatus(500)->withHeader('content-type', 'application/json');
    }

    $response->getBody()->write($jsonResponse);
    return $response->withHeader('Content-Type', 'application/json');
})->add($jsonBodyParser);

/* $app->get('/', function (Request $request, Response $response, $args) {
    $response->getBody()->write("Hello world!");
    return $response;
}); */

$app->run();
