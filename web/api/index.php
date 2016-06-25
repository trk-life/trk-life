<?php
/**
 * Route all http requests to the relevant functions
 *
 * @package TrkLife
 * @author George Webb <george@webb.uno>
 */

require_once dirname(dirname(__DIR__)) . '/app/config/bootstrap.php';

use Slim\App;
use TrkLife\Container;
use TrkLife\Config;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use TrkLife\ErrorHandler;
use Psr7Middlewares\Middleware\TrailingSlash;
use RKA\Middleware\IpAddress;
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

$c['mailer'] = function () {
    return new PHPMailer();
};

//Override the default Not Found Handler
$c['notFoundHandler'] = function ($c) {
    return function ($request, $response) use ($c) {
        return $c->response
            ->withStatus(404)
            ->withHeader('Content-Type', 'text/html')
            ->write('404 Not found');
    };
};

// Override the default Not Allowed Handler
$c['notAllowedHandler'] = function ($c) {
    return function ($request, $response, $methods) use ($c) {
        return $c->response
            ->withStatus(405)
            ->withHeader('Allow', implode(', ', $methods))
            ->withHeader('Content-type', 'text/html')
            ->write('405 Method Not Allowed');
    };
};

$app = new App($c);

// Add app-wide middleware
$app->add(new TrailingSlash(false));
$app->add(new IpAddress(true));

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
$app->get('/users/logout', '\TrkLife\Controller\UserController:logout')->add($auth);
$app->post('/users/forgotten-password', '\TrkLife\Controller\UserController:forgottenPassword');
$app->post('/users/reset-password', '\TrkLife\Controller\UserController:resetPassword');

/**
 * User settings
 */
$app->get('/settings/user/get', '\TrkLife\Controller\UserController:getCurrentUser')->add($auth);
$app->post('/settings/user/update', '\TrkLife\Controller\UserController:updateCurrentUser')->add($auth);
$app->post('/settings/user/change-password', '\TrkLife\Controller\UserController:changeCurrentUsersPassword')->add($auth);
$app->post('/settings/user/delete', '\TrkLife\Controller\UserController:deleteCurrentUser')->add($auth);

/**
 * Team management
 */
$app->get('/team/users/list', '\TrkLife\Controller\TeamController:listUsers')->add($auth);
$app->get('/team/users/{id}/get', '\TrkLife\Controller\TeamController:getUser')->add($auth);
$app->post('/team/users/create', '\TrkLife\Controller\TeamController:createUser')->add($auth);
$app->post('/team/users/{id}/update', '\TrkLife\Controller\TeamController:updateUser')->add($auth);
$app->post('/team/users/{id}/delete', '\TrkLife\Controller\TeamController:deleteUser')->add($auth);

/**
 * Manage project routes
 */
$app->post('/categories/create', '\TrkLife\Controller\CategoryController:create')->add($auth);
$app->post('/categories/{id}/update', '\TrkLife\Controller\CategoryController:update')->add($auth);
$app->post('/categories/{id}/archive', '\TrkLife\Controller\CategoryController:archive')->add($auth);
$app->post('/projects/create', '\TrkLife\Controller\ProjectController:create')->add($auth);
$app->post('/projects/{id}/update', '\TrkLife\Controller\ProjectController:update')->add($auth);
$app->post('/projects/{id}/archive', '\TrkLife\Controller\ProjectController:archive')->add($auth);
$app->post('/items/create', '\TrkLife\Controller\ItemController:create')->add($auth);
$app->post('/items/{id}/update', '\TrkLife\Controller\ItemController:update')->add($auth);
$app->post('/items/{id}/archive', '\TrkLife\Controller\ItemController:archive')->add($auth);

/**
 * Tracking routes
 */
$app->get('/tracking/data', '\TrkLife\Controller\TrackingController:getData')->add($auth);
$app->post('/tracking/save', '\TrkLife\Controller\TrackingController:save')->add($auth);

$app->run();
