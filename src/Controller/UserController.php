<?php

namespace TrkLife\Controller;

use Interop\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class UserController
 *
 * Controller for user functionality
 *
 * @package TrkLife\Controller
 * @author George Webb <george@webb.uno>
 */
class UserController
{
    /**
     * Dependency Inj Container
     *
     * @var ContainerInterface
     */
    private $c;

    /**
     * UserController constructor.
     *
     * @param ContainerInterface $c Dependency Inj Container
     */
    public function __construct(ContainerInterface $c)
    {
        $this->c = $c;
    }

    /**
     * Logs in a user with email address and password
     *
     * @param ServerRequestInterface $request   The request object
     * @param ResponseInterface $response       The response object
     * @return ResponseInterface                The response object
     */
    public function login(ServerRequestInterface $request, ResponseInterface $response)
    {
        $data = $request->getParsedBody();

        // Get and check email and password
        $email = filter_var(empty($data['email']) ? '' : $data['email'], FILTER_SANITIZE_EMAIL);
        $password = filter_var(empty($data['password']) ? '' : $data['password'], FILTER_SANITIZE_STRING);
        if (empty($email) || empty($password)) {
            $response->getBody()->write(json_encode(array(
                'status' => 'fail',
                'message' => 'Invalid email or password'
            )));

            return $response;
        }

        // TODO: login logic

        $response->getBody()->write(json_encode(array(
            'status' => 'success',
            'email' => $email
        )));

        return $response;
    }
}
