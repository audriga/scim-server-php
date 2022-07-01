<?php

namespace Opf\Controllers\JWT;

use Firebase\JWT\JWT;
use Opf\Controllers\Controller;
use Opf\Util\Util;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Psr7\Response;

class GenerateJWTAction extends Controller
{
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        // TODO: It'd be good practice to maybe protect this endpoint with basic auth
        $config = Util::getConfigFile();
        $settings = $config['jwt'];
        $token = JWT::encode(["user" => "admin", "password" => "admin"], $settings['secret']);

        $responseBody = json_encode(['Bearer' => $token], JSON_UNESCAPED_SLASHES);

        $response = new Response($status = 200);
        $response->getBody()->write($responseBody);
        $response = $response->withHeader('Content-Type', 'application/json');
        return $response;
    }
}
