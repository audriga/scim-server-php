<?php

namespace Opf\Middleware;

use Opf\Util\Authentication\SimpleBearerAuthenticator;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;
use Slim\Routing\RouteContext;

class SimpleAuthMiddleware implements MiddlewareInterface
{
    /** @var \Opf\Util\Authentication\SimpleBearerAuthenticator */
    private $bearerAuthenticator;

    public function __construct(ContainerInterface $container)
    {
        $this->bearerAuthenticator = $container->get('BearerAuthenticator');
    }

    public function process(Request $request, RequestHandler $handler): Response
    {
        // If no 'Authorization' header supplied, we directly return a 401
        if (!$request->hasHeader('Authorization')) {
            return new Response(401);
        }

        // $request->getHeader() gives back a string array, hence the need for [0]
        $authHeader = $request->getHeader('Authorization')[0];

        // Obtain the auth type and the supplied credentials
        $authHeaderSplit = explode(' ', $authHeader);
        $authType = $authHeaderSplit[0];
        $authCredentials = $authHeaderSplit[1];

        // This is a flag that tracks whether auth succeeded or not
        $isAuthSuccessful = false;

        $authorizationInfo = [];

        // Call the right authenticator, based on the auth type
        if (strcmp($authType, 'Bearer') === 0) {
            $isAuthSuccessful = $this->bearerAuthenticator->authenticate($authCredentials, $authorizationInfo);
        }

        // If everything went fine, let the request pass through
        if ($isAuthSuccessful) {
            return $handler->handle($request);
        }

        // If something didn't go right so far, then return a 401
        return new Response(401);
    }
}
