<?php

namespace TrkLife\Controller;

use TrkLife\Container;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Http\Response;

/**
 * Class ProjectController
 *
 * Controller for project functionality
 *
 * @package TrkLife\Controller
 * @author George Webb <george@webb.uno>
 */
class ProjectController
{
    /**
     * Dependency Inj Container
     *
     * @var Container
     */
    private $c;

    /**
     * ProjectController constructor.
     *
     * @param Container $c  Dependency Inj Container
     */
    public function __construct(Container $c)
    {
        $this->c = $c;
    }

    /**
     * Create a project
     *
     * @param ServerRequestInterface $request   The request object
     * @param Response $response                The response object
     * @return Response                         The response object
     */
    public function create(ServerRequestInterface $request, Response $response)
    {
        // TODO
    }

    /**
     * Update an existing project
     *
     * @param ServerRequestInterface $request   The request object
     * @param Response $response                The response object
     * @param array $args                       Array of route arguments
     * @return Response                         The response object
     */
    public function update(ServerRequestInterface $request, Response $response, $args)
    {
        // TODO
    }

    /**
     * Archive a project
     *
     * @param ServerRequestInterface $request   The request object
     * @param Response $response                The response object
     * @param array $args                       Array of route arguments
     * @return Response                         The response object
     */
    public function archive(ServerRequestInterface $request, Response $response, $args)
    {
        // TODO
    }
}
