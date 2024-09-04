<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;

require '../../src/utils/RSA/RSAEncryption.php';
require '../../src/utils/exceptions/CustomException.php';

require __DIR__ . '/../../vendor/autoload.php';
require __DIR__ . '/../../MiddleWare/BodyParser.php';

$rsa_algorithm = new RSAEncryption('../../keys/private.key', '../../keys/public.key');

$app = AppFactory::create();

$twig = Twig::create(__DIR__ . '/../../public/templates', ['cache' => false]);

$app->add(TwigMiddleware::create($app, $twig));

$app->get('/', function ($request, $response) {
    $view = Twig::fromRequest($request);
    
    return $view->render($response, 'index.html.twig', [
        'name' => 'John',
    ]);
});

$app->post('/encrypt', function (Request $request, Response $response, $args) use ($rsa_algorithm) {
    $data = $request->getParsedBody();

    $message = $data['message'];

    $responseData = ['message' => "" . $rsa_algorithm->encryptData($message)];
    
    $jsonResponse = json_encode($responseData);

    /* foreach ($responseData as $r => $i) {
        # code...
        error_log("1-" . $i);
    } */
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
    
    $message = $data['message'] ?? 'Guest';
    
    // Create a response
    $responseData = ['message' => $rsa_algorithm->decryptData($message)];
    
    // Set response body as JSON
    $response->getBody()->write(json_encode($responseData));
    return $response->withHeader('Content-Type', 'application/json');
});

/* $app->get('/', function (Request $request, Response $response, $args) {
    $response->getBody()->write("Hello world!");
    return $response;
}); */

$app->run();
