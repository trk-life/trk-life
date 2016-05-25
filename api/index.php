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
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use TrkLife\ErrorHandler;

// DI Container
$c = new Container(array());

// Add logging to DI Container
$c['logger'] = function() {
    $logger = new Logger('trk.life');
    $logger->pushHandler(new StreamHandler(
        Config::get('RootDir') . "/tmp/logs/app.log",
        Logger::toMonologLevel(Config::get('LogLevel'))
    ));
    return $logger;
};

$app = new App($c);

// Replace Slim error handler
unset($app->getContainer()['errorHandler']);
new ErrorHandler($app);

/**
 * Login route
 */
$app->post('/login', '\TrkLife\Controller\UserController:login');

$app->run();
