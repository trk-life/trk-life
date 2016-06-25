<?php

namespace TrkLife\Controller;

use TrkLife\Container;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Http\Response;
use TrkLife\Email\Email;
use TrkLife\Entity\ForgottenPassword;
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
     * @var Container
     */
    private $c;

    /**
     * UserController constructor.
     *
     * @param Container $c  Dependency Inj Container
     */
    public function __construct(Container $c)
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

        /* @var $user User */
        $user = $user_repository->findOneByEmail($email);

        // Check user exists and email is correct
        if ($user === null || !$user->checkPassword($password)) {
            return $response->withJson(array(
                'status' => 'fail',
                'message' => 'A user with this email and password cannot be found.'
            ));
        }

        // Check user is active
        if ($user->get('status') != User::STATUS_ACTIVE) {
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

        $token_entity->set('user_id', $user->get('id'));
        $token_entity->set('token', $token);
        $token_entity->set('expires_after', $expires);
        $token_entity->set('last_accessed', (new \DateTime())->getTimestamp());
        $token_entity->set('user_agent', empty($_SERVER['HTTP_USER_AGENT']) ? '' : $_SERVER['HTTP_USER_AGENT']);

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

    /**
     * Processes a forgotten password request
     *
     * @param ServerRequestInterface $request   The request object
     * @param Response $response                The response object
     * @return Response                         The response object
     */
    public function forgottenPassword(ServerRequestInterface $request, Response $response)
    {
        $data = $request->getParsedBody();

        // Get and check email
        $email = filter_var(empty($data['email']) ? '' : $data['email'], FILTER_SANITIZE_EMAIL);
        if (empty($email)) {
            return $response->withJson(array(
                'status' => 'fail',
                'message' => 'Invalid email address'
            ));
        }

        // Get user from DB
        $user_repository = $this->c->EntityManager->getRepository('TrkLife\Entity\User');

        /* @var $user User */
        $user = $user_repository->findOneByEmail($email);

        // Check user exists and email is correct
        if ($user === null) {
            return $response->withJson(array(
                'status' => 'fail',
                'message' => 'A user with this email address cannot be found.'
            ));
        }

        // Create forgotten password entity
        $forgotten_password_request = new ForgottenPassword();
        $token = $forgotten_password_request->generateToken();
        $forgotten_password_request->set('email', $email);
        $forgotten_password_request->set('token', $token);
        $forgotten_password_request->set('ip_address', $request->getAttribute('ip_address'));
        $forgotten_password_request->set('user_agent', empty($_SERVER['HTTP_USER_AGENT']) ? '' : $_SERVER['HTTP_USER_AGENT']);
        $forgotten_password_request->set('status', ForgottenPassword::STATUS_SUBMITTED);

        // TODO: rate limit by email and ip

        // Persist entity
        try {
            $this->c->EntityManager->persist($forgotten_password_request);
            $this->c->EntityManager->flush();
        } catch (\Exception $e) {
            return $response->withJson(array(
                'status' => 'fail',
                'message' => 'There was a problem processing your forgotten password request, please try again later.'
            ));
        }

        // Create link
        $uri = $request->getUri();
        $protocol = $uri->getScheme();
        $host = $uri->getHost();
        $port = $uri->getPort() == '80' ? '' : ':' . $uri->getPort();
        $link = "$protocol://$host$port/#reset-password?token=$token"; // TODO use real link from app

        // Send email
        $email_result = Email::create(
            $this->c,
            $email,
            $user->get('first_name') . ' ' . $user->get('last_name'),
            'forgotten_password',
            array('link' => $link, 'name' => $user->get('first_name'))
        );

        if (!$email_result) {
            return $response->withJson(array(
                'status' => 'fail',
                'message' => 'There was a problem processing your forgotten password request, please try again later.'
            ));
        }

        return $response->withJson(array(
            'status' => 'success',
            'message' => 'An email has been sent to this address containing a link to reset your password.'
        ));
    }

    /**
     * Resets a password from a forgotten password request
     *
     * @param ServerRequestInterface $request   The request object
     * @param Response $response                The response object
     * @return Response                         The response object
     */
    public function resetPassword(ServerRequestInterface $request, Response $response)
    {
        $data = $request->getParsedBody();

        // Get and check email and password
        $token = filter_var(empty($data['token']) ? '' : $data['token'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $new_password = filter_var(empty($data['new_password']) ? '' : $data['new_password'], FILTER_SANITIZE_STRING);
        if (empty($token) || empty($new_password)) {
            return $response->withJson(array(
                'status' => 'fail',
                'message' => 'Please make sure all fields are complete.'
            ));
        }

        // Get request from DB
        $forgotten_password_repository = $this->c->EntityManager->getRepository('TrkLife\Entity\ForgottenPassword');

        /* @var $forgotten_password ForgottenPassword */
        $forgotten_password = $forgotten_password_repository->findOneByToken($token);

        if ($forgotten_password == null) {
            return $response->withJson(array(
                'status' => 'fail',
                'message' => 'Your link is invalid, please try again.'
            ));
        }

        if ($forgotten_password->get('status') != ForgottenPassword::STATUS_SUBMITTED) {
            return $response->withJson(array(
                'status' => 'fail',
                'message' => 'Your link has already been used, please try again.'
            ));
        }

        if ($forgotten_password->get('created') < (new \DateTime())->sub(new \DateInterval('P1D'))->getTimestamp()) {
            return $response->withJson(array(
                'status' => 'fail',
                'message' => 'Your link has expired, please try again.'
            ));
        }

        // Get user from DB
        $user_repository = $this->c->EntityManager->getRepository('TrkLife\Entity\User');

        /* @var $user User */
        $user = $user_repository->findOneByEmail($forgotten_password->get('email'));

        if ($user == null) {
            return $response->withJson(array(
                'status' => 'fail',
                'message' => 'There was a problem, please try again.'
            ));
        }

        // Save the new password
        if (!$user->set('password', $new_password)) {
            return $response->withJson(array(
                'status' => 'fail',
                'message' => 'New password must be at least 8 characters long.'
            ));
        }

        // Update the forgotten password request
        $forgotten_password->set('status', ForgottenPassword::STATUS_USED);

        try {
            $this->c->EntityManager->persist($user);
            $this->c->EntityManager->persist($forgotten_password);
            $this->c->EntityManager->flush();
        } catch (\Exception $e) {
            return $response->withJson(array(
                'status' => 'fail',
                'message' => 'There was a problem, please try again.'
            ));
        }

        return $response->withJson(array(
            'status' => 'success',
            'message' => 'Successfully reset password.'
        ));
    }

    /**
     * Get the currently logged in user's details
     *
     * @param ServerRequestInterface $request   The request object
     * @param Response $response                The response object
     * @return Response                         The response object
     */
    public function getCurrentUser(ServerRequestInterface $request, Response $response)
    {
        // TODO
    }

    /**
     * Update the currently logged in user
     *
     * @param ServerRequestInterface $request   The request object
     * @param Response $response                The response object
     * @return Response                         The response object
     */
    public function updateCurrentUser(ServerRequestInterface $request, Response $response)
    {
        // TODO
    }

    /**
     * Change the currently logged in user's password
     *
     * @param ServerRequestInterface $request   The request object
     * @param Response $response                The response object
     * @return Response                         The response object
     */
    public function changeCurrentUsersPassword(ServerRequestInterface $request, Response $response)
    {
        // TODO
    }

    /**
     * Delete the currently logged in user
     *
     * @param ServerRequestInterface $request   The request object
     * @param Response $response                The response object
     * @return Response                         The response object
     */
    public function deleteCurrentUser(ServerRequestInterface $request, Response $response)
    {
        // TODO
    }
}
