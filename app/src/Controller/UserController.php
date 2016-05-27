<?php

namespace TrkLife\Controller;

use Interop\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Http\Response;
use TrkLife\Entity\User;

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
     * @param Response $response                The response object
     * @return Response                         The response object
     */
    public function login(ServerRequestInterface $request, Response $response)
    {
        $data = $request->getParsedBody();

        // Get and check email and password
        $email = filter_var(empty($data['email']) ? '' : $data['email'], FILTER_SANITIZE_EMAIL);
        $password = filter_var(empty($data['password']) ? '' : $data['password'], FILTER_SANITIZE_STRING);
        if (empty($email) || empty($password)) {
            return $response->withJson(array(
                'status' => 'fail',
                'message' => 'Invalid email or password'
            ));
        }

        // TODO: login logic

        return $response->withJson(array(
            'status' => 'success',
            'email' => $email
        ));
    }

    /**
     * Creates a new user
     *
     * @param ServerRequestInterface $request   The request object
     * @param Response $response                The response object
     * @return Response                         The response object
     */
    public function create(ServerRequestInterface $request, Response $response)
    {
        // TODO: proper implementation

        $user = new User;
        $user->setEmail('george@gawdev.co.uk');
        $user->setPassword('password');
        $user->setFirstName('George');
        $user->setLastName('Webb');
        $user->setStatus('active');

        // Save the user
        $this->c->EntityManager->persist($user);
        $this->c->EntityManager->flush();

        return $response->withJson(array(
            'status' => 'success',
            'user' => $user->getAttributes()
        ));
    }
}
