<?php

namespace TrkLife;

use Interop\Container\ContainerInterface;
use Exception;
use Psr\Log\LogLevel;
use Slim\App;

/**
 * Class ErrorHandler
 *
 * @package TrkLife
 * @author George Webb <george@webb.uno>
 */
class ErrorHandler
{
    /**
     * @var App
     */
    private $app;

    /**
     * The DI container
     *
     * @var ContainerInterface
     */
    private $c;

    /**
     * Holds 20kb of memory for use for handling fatal errors, specifically out of memory errors
     *
     * @var string
     */
    private $reserved_memory;

    /**
     * A list of error types which can be treated as fatal
     *
     * @var array
     */
    private static $fatal_errors = array(E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR);

    /**
     * A map of php error codes to PSR log levels
     *
     * @var array
     */
    private static $map_error_to_log_level = array(
        E_ERROR             => LogLevel::CRITICAL,
        E_WARNING           => LogLevel::WARNING,
        E_PARSE             => LogLevel::ALERT,
        E_NOTICE            => LogLevel::NOTICE,
        E_CORE_ERROR        => LogLevel::CRITICAL,
        E_CORE_WARNING      => LogLevel::WARNING,
        E_COMPILE_ERROR     => LogLevel::ALERT,
        E_COMPILE_WARNING   => LogLevel::WARNING,
        E_USER_ERROR        => LogLevel::ERROR,
        E_USER_WARNING      => LogLevel::WARNING,
        E_USER_NOTICE       => LogLevel::NOTICE,
        E_STRICT            => LogLevel::NOTICE,
        E_RECOVERABLE_ERROR => LogLevel::ERROR,
        E_DEPRECATED        => LogLevel::NOTICE,
        E_USER_DEPRECATED   => LogLevel::NOTICE
    );

    /**
     * Maps php error codes to corresponding strings
     *
     * @var array
     */
    private static $map_error_to_string = array(
        E_ERROR             => 'E_ERROR',
        E_WARNING           => 'E_WARNING',
        E_PARSE             => 'E_PARSE',
        E_NOTICE            => 'E_NOTICE',
        E_CORE_ERROR        => 'E_CORE_ERROR',
        E_CORE_WARNING      => 'E_CORE_WARNING',
        E_COMPILE_ERROR     => 'E_COMPILE_ERROR',
        E_COMPILE_WARNING   => 'E_COMPILE_WARNING',
        E_USER_ERROR        => 'E_USER_ERROR',
        E_USER_WARNING      => 'E_USER_WARNING',
        E_USER_NOTICE       => 'E_USER_NOTICE',
        E_STRICT            => 'E_STRICT',
        E_RECOVERABLE_ERROR => 'E_RECOVERABLE_ERROR',
        E_DEPRECATED        => 'E_DEPRECATED',
        E_USER_DEPRECATED   => 'E_USER_DEPRECATED'
    );

    /**
     * ErrorHandler constructor.
     *
     * @param App $app  The Slim App
     */
    public function __construct(App $app)
    {
        error_reporting(0);
        $this->app = $app;
        $this->c = $app->getContainer();
    }

    /**
     * Registers error handler, fatal error handler and exception handler
     */
    public function register()
    {
        $this->registerErrorHandler();
        $this->registerExceptionHandler();
        $this->registerFatalHandler();
    }

    /**
     * Register error handler
     */
    public function registerErrorHandler()
    {
        set_error_handler(array($this, 'handleError'), -1);
    }

    /**
     * Register exception handler
     */
    public function registerExceptionHandler()
    {
        set_exception_handler(array($this, 'handleException'));
    }

    /**
     * Register fatal error handler
     */
    public function registerFatalHandler()
    {
        register_shutdown_function(array($this, 'handleFatalError'));
        $this->reserved_memory = str_repeat(' ', 1024 * 20);
    }

    /**
     * Handle php errors
     *
     * @param int $code         The error code
     * @param string $message   The error message
     * @param string $file      The file the error occured in
     * @param int $line         The line of the error
     * @param array $context    Any variables relevant to the error
     */
    public function handleError($code, $message, $file = '', $line = 0, $context = array())
    {
        // Check if not a fatal error, these get handled by the shutdown func
        if (in_array($code, static::$fatal_errors, true)) {
            return;
        }

        // Log the error
        $level = isset(static::$map_error_to_log_level[$code]) ?
            static::$map_error_to_log_level[$code] : LogLevel::CRITICAL;
        $this->c->logger->log($level, static::$map_error_to_string[$code] . ': ' . $message, array(
            'code' => $code,
            'message' => $message,
            'file' => $file,
            'line' => $line
        ));
    }

    /**
     * Handle uncaught exceptions
     *
     * @param Exception $e  The uncaught exception
     */
    public function handleException(Exception $e)
    {
        // Log the uncaught exception
        $this->c->logger->log(
            LogLevel::ERROR,
            sprintf(
                'Uncaught Exception %s: "%s" at %s line %s',
                get_class($e),
                $e->getMessage(),
                $e->getFile(),
                $e->getLine()
            ),
            array('exception' => $e)
        );

        // 500 error
        $this->error500();
    }

    /**
     * Handles a fatal error
     */
    public function handleFatalError()
    {
        // Free up our reserved memory
        $this->reserved_memory = null;

        // Check last error is fatal, as this function is called on every script running
        $last_error = error_get_last();
        if (is_array($last_error) && in_array($last_error['type'], static::$fatal_errors, true)) {
            // Log the error
            $this->c->logger->log(
                LogLevel::ALERT,
                'Fatal Error (' . static::$map_error_to_string[$last_error['type']] . '): ' . $last_error['message'],
                array(
                    'code' => $last_error['type'],
                    'message' => $last_error['message'],
                    'file' => $last_error['file'],
                    'line' => $last_error['line']
                )
            );

            // 500 error
            $this->error500();
        }
    }

    /**
     * Returns 500 error
     */
    private function error500()
    {
        $this->app->respond(
            $this->c['response']->withStatus(500)
                ->withHeader('Content-Type', 'text/html')
                ->write('500 Internal Server Error')
        );
    }
}
