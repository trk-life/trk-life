<?php

namespace TrkLife\Controller;

use TrkLife\Container;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Http\Response;

/**
 * Class TeamController
 *
 * Controller for team functionality
 *
 * @package TrkLife\Controller
 * @author George Webb <george@webb.uno>
 */
class TeamController
{
    /**
     * Dependency Inj Container
     *
     * @var Container
     */
    private $c;

    /**
     * TeamController constructor.
     *
     * @param Container $c  Dependency Inj Container
     */
    public function __construct(Container $c)
    {
        $this->c = $c;
    }

    /**
     * List the team's users
     *
     * @param ServerRequestInterface $request   The request object
     * @param Response $response                The response object
     * @return Response                         The response object
     */
    public function listUsers(ServerRequestInterface $request, Response $response)
    {
        // TODO
    }

    /**
     * Get the details of a single user in the team
     *
     * @param ServerRequestInterface $request   The request object
     * @param Response $response                The response object
     * @param array $args                       Array of route arguments
     * @return Response                         The response object
     */
    public function getUser(ServerRequestInterface $request, Response $response, $args)
    {
        // TODO
    }

    /**
     * Create new team user
     *
     * @param ServerRequestInterface $request   The request object
     * @param Response $response                The response object
     * @return Response                         The response object
     */
    public function createUser(ServerRequestInterface $request, Response $response)
    {
        // TODO
    }

    /**
     * Update a user in the team
     *
     * @param ServerRequestInterface $request   The request object
     * @param Response $response                The response object
     * @param array $args                       Array of route arguments
     * @return Response                         The response object
     */
    public function updateUser(ServerRequestInterface $request, Response $response, $args)
    {
        // TODO
    }

    /**
     * Delete a team user
     *
     * @param ServerRequestInterface $request   The request object
     * @param Response $response                The response object
     * @param array $args                       Array of route arguments
     * @return Response                         The response object
     */
    public function deleteUser(ServerRequestInterface $request, Response $response, $args)
    {
        // TODO
    }
}
