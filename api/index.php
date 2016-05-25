<?php
/**
 * Route all http requests to the relevant functions
 *
 * @package TrkLife
 * @author George Webb <george@webb.uno>
 */

require_once dirname(__DIR__) . '/config/bootstrap.php';

use Slim\App;
use Slim\Container;
use TrkLife\Config;

$slim_conf = array(
    'settings' => array(
        'displayErrorDetails' => Config::get('DisplayErrors'),
    ),
);

$c = new Container($slim_conf);
$app = new App($c);

/**
 * Login route
 */
$app->post('/login', '\TrkLife\Controller\UserController:login');

$app->run();
