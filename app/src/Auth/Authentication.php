<?php

namespace TrkLife\Auth;

use Interop\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class Authentication
 *
 * @package TrkLife\Auth
 * @author George Webb <george@webb.uno>
 */
class Authentication
{
    /**
     * @var ContainerInterface
     */
    private $c;

    /**
     * Authentication constructor.
     * @param ContainerInterface $c
     */
    public function __construct(ContainerInterface $c)
    {
        $this->c = $c;
    }

    /**
     * Authenticates the request
     *
     * @param ServerRequestInterface $request
     * @return bool
     */
    public function authenticate(ServerRequestInterface $request)
    {
        // TODO
        return true;
    }
}
