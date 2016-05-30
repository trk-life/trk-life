<?php

namespace TrkLife\Auth;

use Interop\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class Authorisation
 *
 * @package TrkLife\Auth
 * @author George Webb <george@webb.uno>
 */
class Authorisation
{
    /**
     * @var ContainerInterface
     */
    private $c;

    /**
     * Authorisation constructor.
     * @param ContainerInterface $c
     */
    public function __construct(ContainerInterface $c)
    {
        $this->c = $c;
    }

    /**
     * Authorises a request, ensuring that the user's role is sufficient
     *
     * @param ServerRequestInterface $request
     * @return bool | ServerRequestInterface    False on failure, request on success
     */
    public function authorise(ServerRequestInterface $request)
    {
        // TODO
        return $request;
    }
}
