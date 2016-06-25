<?php

namespace TrkLife\Auth;

use TrkLife\Container;
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
     * @var Container
     */
    private $c;

    /**
     * Authentication constructor.
     * @param Container $c
     */
    public function __construct(Container $c)
    {
        $this->c = $c;
    }

    /**
     * Authenticates the request
     *
     * @param ServerRequestInterface $request
     * @return bool | ServerRequestInterface    False on failure, request on success
     */
    public function authenticate(ServerRequestInterface $request)
    {
        $auth_header = $request->getHeader('Authorization');
        $token = str_replace('Bearer ', '', (is_array($auth_header) ? array_pop($auth_header) : $auth_header));

        if (empty($token)) {
            return false;
        }

        $token_repo = $this->c->EntityManager->getRepository('TrkLife\Entity\Token');
        $token_entity = $token_repo->findOneByToken($token);

        if ($token_entity === null) {
            return false;
        }

        // Set user and token in request attribute
        $user_repository = $this->c->EntityManager->getRepository('TrkLife\Entity\User');
        $user = $user_repository->findOneById($token_entity->get('user_id'));

        // Check user exists
        if ($user === null) {
            return false;
        }

        $request = $request->withAttribute('user', $user);
        $request = $request->withAttribute('token_entity', $token_entity);

        return $request;
    }
}
