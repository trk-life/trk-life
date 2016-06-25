<?php

namespace TrkLife\Controller;

use TrkLife\Container;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Http\Response;

/**
 * Class TrackingController
 *
 * Controller for tracking functionality
 *
 * @package TrkLife\Controller
 * @author George Webb <george@webb.uno>
 */
class TrackingController
{
    /**
     * Dependency Inj Container
     *
     * @var Container
     */
    private $c;

    /**
     * TrackingController constructor.
     *
     * @param Container $c  Dependency Inj Container
     */
    public function __construct(Container $c)
    {
        $this->c = $c;
    }

    /**
     * Receive all tracking data between two dates, inclusive. The data returns all levels for that time period for the
     * user, i.e. it will return all categories, all projects within each category, and all items within each project,
     * as well as all hours tracked between that time, and each day's journal entry.
     *
     * @param ServerRequestInterface $request   The request object
     * @param Response $response                The response object
     * @return Response                         The response object
     */
    public function data(ServerRequestInterface $request, Response $response)
    {
        // TODO
    }

    /**
     * Save hours to an item on a given day.
     *
     * @param ServerRequestInterface $request   The request object
     * @param Response $response                The response object
     * @return Response                         The response object
     */
    public function save(ServerRequestInterface $request, Response $response)
    {
        // TODO
    }
}
