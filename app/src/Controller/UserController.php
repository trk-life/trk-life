<?php

namespace TrkLife\Controller;

use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Interop\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Http\Response;
use TrkLife\Entity\Token;
use TrkLife\Entity\User;
use TrkLife\Exception\ValidationException;

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
        $stay_logged_in = filter_var(empty($data['stay_logged_in']) ? 0 : $data['stay_logged_in'], FILTER_SANITIZE_NUMBER_INT);
        if (empty($email) || empty($password)) {
            return $response->withJson(array(
                'status' => 'fail',
                'message' => 'Invalid email or password'
            ));
        }

        // Get user from DB
        $user_repository = $this->c->EntityManager->getRepository('TrkLife\Entity\User');
        $user = $user_repository->findOneByEmail($email);

        // Check user exists and email is correct
        if ($user === null || !$user->checkPassword($password)) {
            return $response->withJson(array(
                'status' => 'fail',
                'message' => 'A user with this email and password cannot be found.'
            ));
        }

        // Check user is active
        if ($user->getStatus() != User::STATUS_ACTIVE) {
            return $response->withJson(array(
                'status' => 'fail',
                'message' => 'This user is currently disabled.'
            ));
        }

        // TODO: rate limiting, save login attempt - consider UserRepository class

        // Expiry time - 1 day normal, 90 days if stay logged in.
        $expires = $stay_logged_in ? 60 * 60 * 24 * 90 : 60 * 60 * 24;

        // Create token
        $token_entity = new Token();
        $token = $token_entity->generateToken();

        $token_entity->setUserId($user->getId());
        $token_entity->setToken($token);
        $token_entity->setExpiresAfter($expires);
        $token_entity->setLastAccessed((new \DateTime())->getTimestamp());
        $token_entity->setUserAgent(empty($_SERVER['HTTP_USER_AGENT']) ? '' : $_SERVER['HTTP_USER_AGENT']);

        try {
            // Save the token
            $this->c->EntityManager->persist($token_entity);
            $this->c->EntityManager->flush();
        } catch (\Exception $e) {
            return $response->withJson(array(
                'status' => 'fail',
                'message' => 'There was a problem logging in, please try again later.'
            ));
        }

        return $response->withJson(array(
            'status' => 'success',
            'user' => $user->getAttributes(),
            'token' => $token
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
        $user->setEmail('george@webb.uno');
        if (!$user->setPassword('password')) {
            return $response->withJson(array(
                'status' => 'fail',
                'validation_messages' => array('Password must be at least 8 characters long.')
            ));
        }
        $user->setFirstName('George');
        $user->setLastName('Webb');
        $user->setRole('admin');
        $user->setStatus('active');

        try {
            // Save the user
            $this->c->EntityManager->persist($user);
            $this->c->EntityManager->flush();
        } catch (ValidationException $e) {
            return $response->withJson(array(
                'status' => 'fail',
                'validation_messages' => $e->validation_messages
            ));
        } catch (UniqueConstraintViolationException $e) {
            return $response->withJson(array(
                'status' => 'fail',
                'validation_messages' => array('Email address is already registered.')
            ));
        }

        return $response->withJson(array(
            'status' => 'success',
            'user' => $user->getAttributes()
        ));
    }
}
