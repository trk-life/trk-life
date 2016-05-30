<?php

namespace TrkLife\Auth;

use Interop\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr7Middlewares\Middleware;
use Slim\Http\Response;

/**
 * Class AuthMiddleware
 *
 * @package TrkLife\Auth
 * @author George Webb <george@webb.uno>
 */
class AuthMiddleware
{
    /**
     * @var ContainerInterface
     */
    private $c;

    /**
     * AuthMiddleware constructor.
     * @param ContainerInterface $c
     */
    public function __construct(ContainerInterface $c)
    {
        $this->c = $c;
    }

    /**
     * Authenticates and authorises the request
     *
     * @param ServerRequestInterface $request   The request
     * @param Response $response                The response
     * @param callable $next                    The next middleware to call
     * @return Response                         The response
     */
    public function __invoke(ServerRequestInterface $request, Response $response, $next)
    {
        // Authentication
        $authentication = new Authentication($this->c);
        $result = $authentication->authenticate($request);
        if ($result === false) {
            // Not authenticated
            return $response
                ->withStatus(401)
                ->withJson(json_encode("401 Unauthorized"));
        }
        $request = $result;

        // Authorisation
        $authorisation = new Authorisation($this->c);
        $result = $authorisation->authorise($request);
        if ($result === false) {
            // Not authorised
            return $response
                ->withStatus(403)
                ->withJson(json_encode("403 Unauthorized"));
        }
        $request = $result;

        return $next($request, $response);
    }
}
