<?php
/**
 * Route all http requests to the relevant functions
 *
 * @package TrkLife
 * @author George Webb <george@webb.uno>
 */

require_once dirname(dirname(__DIR__)) . '/app/config/bootstrap.php';

use Slim\App;
use Slim\Container;
use TrkLife\Config;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use TrkLife\ErrorHandler;
use Psr7Middlewares\Middleware\TrailingSlash;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;

// DI Container
$c = new Container(array());

// Add logging to DI Container
$c['logger'] = function () {
    $logger = new Logger('trk.life');
    $logger->pushHandler(new StreamHandler(
        Config::get('RootDir') . "/tmp/logs/app.log",
        Logger::toMonologLevel(Config::get('LogLevel'))
    ));
    return $logger;
};

// Add DB to DI Container
$c['EntityManager'] = function () {
    $conf = Setup::createAnnotationMetadataConfiguration(array(Config::get('AppDir') . '/src/Entity'));
    $conn = array(
        'driver'=> 'pdo_mysql',
        'host'      => Config::get('Database.host'),
        'port'      => Config::get('Database.port'),
        'user'      => Config::get('Database.user'),
        'password'  => Config::get('Database.password'),
        'dbname'    => Config::get('Database.database')
    );
    return EntityManager::create($conn, $conf);
};

//Override the default Not Found Handler
$c['notFoundHandler'] = function ($c) {
    return function ($request, $response) use ($c) {
        return $c['response']
            ->withStatus(404)
            ->withHeader('Content-Type', 'text/html')
            ->write('404 Not found');
    };
};

// Override the default Not Allowed Handler
$c['notAllowedHandler'] = function ($c) {
    return function ($request, $response, $methods) use ($c) {
        return $c['response']
            ->withStatus(405)
            ->withHeader('Allow', implode(', ', $methods))
            ->withHeader('Content-type', 'text/html')
            ->write('405 Method Not Allowed');
    };
};

$app = new App($c);

// Handle trailing slashes
$app->add(new TrailingSlash(false));

// Replace Slim error handler
unset($app->getContainer()['errorHandler']);
new ErrorHandler($app);

/**
 * Login route
 */
$app->post('/users/login', '\TrkLife\Controller\UserController:login');

/**
 * Create user route
 */
$app->post('/users/create', '\TrkLife\Controller\UserController:create'); // TODO: auth

$app->run();
