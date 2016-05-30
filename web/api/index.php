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
use TrkLife\Auth\AuthMiddleware;

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
$error_handler = new ErrorHandler($app);
$error_handler->register();

// Init auth middleware
$auth = new AuthMiddleware($c);

/**
 * User auth routes
 */
$app->post('/users/login', '\TrkLife\Controller\UserController:login');
$app->get('/users/validate-token', '\TrkLife\Controller\UserController:validateToken')->add($auth);
$app->get('/users/logout', '\TrkLife\Controller\UserController:logout');
$app->post('/users/forgotten-password', '\TrkLife\Controller\UserController:forgottenPassword');
$app->post('/users/reset-password', '\TrkLife\Controller\UserController:resetPassword');

/**
 * User management routes
 */
$app->get('/users/get/{id}', '\TrkLife\Controller\UserController:get')->add($auth);
$app->get('/users/list', '\TrkLife\Controller\UserController:list')->add($auth);
$app->post('/users/create', '\TrkLife\Controller\UserController:create')->add($auth);
$app->post('/users/update/{id}', '\TrkLife\Controller\UserController:update')->add($auth);
$app->post('/users/delete/{id}', '\TrkLife\Controller\UserController:delete')->add($auth);

$app->run();
