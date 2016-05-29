<?php

namespace TrkLife;

use Monolog\Handler\TestHandler;
use Monolog\Logger;
use Slim\App;
use Slim\Container;

class ErrorHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Mocked slim app
     *
     * @var App
     */
    private $app;

    /**
     * @var ErrorHandler
     */
    private $error_handler;

    /**
     * @var TestHandler Test Log handler
     */
    private $test_log_handler;

    /**
     * Setup up before each test
     */
    public function setUp()
    {
        $this->app = $this
            ->getMockBuilder('Slim\App')
            ->disableOriginalConstructor()
            ->setMethods(array('getContainer', 'respond'))
            ->getMock();

        // Add logging to DI Container
        $c = new Container();
        $this->test_log_handler = $th = new TestHandler();
        $c['logger'] = function () use ($th) {
            $logger = new Logger('trk.life');
            $logger->pushHandler($th);
            return $logger;
        };

        $this->app
            ->method('getContainer')
            ->willReturn($c);

        $this->error_handler = new ErrorHandler($this->app);
    }

    /**
     * Tear down after each test
     */
    public function tearDown()
    {
        $this->test_log_handler->clear();
    }

    /**
     * Test error handler
     */
    public function testHandleError()
    {
        // Check all error levels
        $this->error_handler->handleError(E_ERROR, 'e-error', __FILE__, __LINE__);
        $this->error_handler->handleError(E_WARNING, 'e-warning', __FILE__, __LINE__);
        $this->error_handler->handleError(E_PARSE, 'e-parse', __FILE__, __LINE__);
        $this->error_handler->handleError(E_NOTICE, 'e-notice', __FILE__, __LINE__);
        $this->error_handler->handleError(E_CORE_ERROR, 'e-core-error', __FILE__, __LINE__);
        $this->error_handler->handleError(E_CORE_WARNING, 'e-core-warning', __FILE__, __LINE__);
        $this->error_handler->handleError(E_COMPILE_ERROR, 'e-compile-error', __FILE__, __LINE__);
        $this->error_handler->handleError(E_COMPILE_WARNING, 'e-compile-warning', __FILE__, __LINE__);
        $this->error_handler->handleError(E_USER_ERROR, 'e-user-error', __FILE__, __LINE__);
        $this->error_handler->handleError(E_USER_WARNING, 'e-user-warning', __FILE__, __LINE__);
        $this->error_handler->handleError(E_USER_NOTICE, 'e-user-notice', __FILE__, __LINE__);
        $this->error_handler->handleError(E_STRICT, 'e-strict', __FILE__, __LINE__);
        $this->error_handler->handleError(E_RECOVERABLE_ERROR, 'e-recoverable-error', __FILE__, __LINE__);
        $this->error_handler->handleError(E_DEPRECATED, 'e-deprecated', __FILE__, __LINE__);
        $this->error_handler->handleError(E_USER_DEPRECATED, 'e-user-deprecated', __FILE__, __LINE__);

        // Some should log, others shouldn't as they should drop into fatal error handler
        // Should have:
        $this->assertTrue($this->test_log_handler->hasRecordThatMatches('/e-warning/', Logger::WARNING));
        $this->assertTrue($this->test_log_handler->hasRecordThatMatches('/e-notice/', Logger::NOTICE));
        $this->assertTrue($this->test_log_handler->hasRecordThatMatches('/e-core-warning/', Logger::WARNING));
        $this->assertTrue($this->test_log_handler->hasRecordThatMatches('/e-compile-warning/', Logger::WARNING));
        $this->assertTrue($this->test_log_handler->hasRecordThatMatches('/e-user-warning/', Logger::WARNING));
        $this->assertTrue($this->test_log_handler->hasRecordThatMatches('/e-user-notice/', Logger::NOTICE));
        $this->assertTrue($this->test_log_handler->hasRecordThatMatches('/e-strict/', Logger::NOTICE));
        $this->assertTrue($this->test_log_handler->hasRecordThatMatches('/e-recoverable-error/', Logger::ERROR));
        $this->assertTrue($this->test_log_handler->hasRecordThatMatches('/e-deprecated/', Logger::NOTICE));
        $this->assertTrue($this->test_log_handler->hasRecordThatMatches('/e-user-deprecated/', Logger::NOTICE));

        // Shouldn't have:
        $this->assertFalse($this->test_log_handler->hasRecordThatMatches('/e-error/', Logger::CRITICAL));
        $this->assertFalse($this->test_log_handler->hasRecordThatMatches('/e-core-error/', Logger::CRITICAL));
        $this->assertFalse($this->test_log_handler->hasRecordThatMatches('/e-user-error/', Logger::ERROR));
        $this->assertFalse($this->test_log_handler->hasRecordThatMatches('/e-compile-error/', Logger::ALERT));
        $this->assertFalse($this->test_log_handler->hasRecordThatMatches('/e-parse/', Logger::ALERT));
    }

    /**
     * Test exception handler
     */
    public function testHandleException()
    {
        $this->error_handler->handleException(new \Exception('exc-message'));
        $this->assertTrue($this->test_log_handler->hasRecordThatMatches('/exc-message/', Logger::ERROR));
    }

    /**
     * Test error 500
     */
    public function testError500()
    {
        $this->app
            ->expects($this->once())
            ->method('respond')
            ->with($this->isInstanceOf('Slim\Http\Response'));

        // Error500 is called from exception handler
        $this->error_handler->handleException(new \Exception('exc-message'));
    }
}
