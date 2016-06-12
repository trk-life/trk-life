<?php

namespace TrkLife;

use Slim\Http\Environment;
use Slim\Http\Request;
use Slim\Http\Response;
use TrkLife\Entity\Token;
use TrkLife\Entity\User;

/**
 * Class TestUtilsTrait
 *
 * Contains utilities for phpunit test cases
 *
 * @package TrkLife
 * @author George Webb <george@webb.uno>
 */
trait TestUtilsTrait
{
    /**
     * @param bool $with_user   Whether or not to create an attribute for a logged in user
     * @param array $user_data  Array of values to override on the environment mock
     * @param array $post_data  POST data - if set, the request will be POST method, and this is the form data
     * @return Request          A Slim Request object
     */
    public function createRequest($with_user = true, $user_data = array(), $post_data = array())
    {
        if (!empty($post_data)) {
            $user_data = array_merge(
                array('REQUEST_METHOD' => 'POST'),
                $user_data
            );
        }

        $request = Request::createFromEnvironment(Environment::mock($user_data));

        if (!empty($post_data)) {
            $request = $request->withParsedBody($post_data);
        }

        if ($with_user) {
            $user = new User();
            $user->set('id', 6);
            $user->set('first_name', 'George');
            $user->set('last_name', 'Webb');
            $user->set('email', 'george@webb.uno');
            $user->set('password', '12345678');
            $user->set('status', User::STATUS_ACTIVE);
            $user->set('role', User::ROLE_ADMIN);
            $user->set('created', 1234567890);
            $user->set('modified', 1234567890);
            $request = $request->withAttribute('user', $user);

            $token = new Token();
            $token->set('id', 78);
            $token->set('user_id', 4);
            $token->set('token', $token->generateToken());
            $token->set('expires_after', 60 * 60 * 24);
            $token->set('last_accessed', time());
            $token->set('user_agent', 'Big Dave\'s Browser V1');
            $token->set('created', time());

            $request = $request->withAttribute('token_entity', $token);
        }

        return $request;
    }

    /**
     * @param int $status_code  The http status code
     * @return Response         A slim Response object
     */
    public function createResponse($status_code = 200)
    {
        return new Response($status_code);
    }

    public function createRepositoryMock()
    {
        return $this
            ->getMockBuilder('\Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->setMethods(array('__call'))
            ->getMock();
    }
}
