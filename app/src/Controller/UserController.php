<?php

namespace TrkLife\Controller;

use Interop\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Http\Response;
use TrkLife\Entity\Token;
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
     * Checks a token is still valid
     *
     * @param ServerRequestInterface $request   The request object
     * @param Response $response                The response object
     * @return Response                         The response object
     */
    public function validateToken(ServerRequestInterface $request, Response $response)
    {
        return $response->withJson(array(
            'status' => 'success',
            'message' => 'Token is valid',
            'user' => $request->getAttribute('user')->getAttributes()
        ));
    }

    /**
     * Logs out the current user
     *
     * @param ServerRequestInterface $request   The request object
     * @param Response $response                The response object
     * @return Response                         The response object
     */
    public function logout(ServerRequestInterface $request, Response $response)
    {
        // Delete token from database
        $this->c->EntityManager->remove($request->getAttribute('token_entity'));
        $this->c->EntityManager->flush();

        return $response->withJson(array(
            'status' => 'success',
            'message' => 'Successfully logged out.'
        ));
    }
}
