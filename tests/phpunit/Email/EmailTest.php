<?php

namespace TrkLife\Email;

use Monolog\Handler\TestHandler;
use Monolog\Logger;
use TrkLife\Config;
use TrkLife\Container;

/**
 * Class EmailTest
 *
 * @package TrkLife\Email
 * @author George Webb <george@webb.uno>
 */
class EmailTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Container
     */
    public $c;

    /**
     * Set up before each test
     */
    public function setUp()
    {
        $this->c = new Container();

        // Mocked logger
        $this->c['logger'] = function () {
            $logger = new Logger('TestLogger');
            $logger->pushHandler(new TestHandler());
            return $logger;
        };

        // Mocked mailer
        $this->c['mailer'] = function ($this) {
            $mailer = $this
                ->getMockBuilder('\PHPMailer')
                ->setMethods(
                    array('send')
                )
                ->getMock();

            return $mailer;
        };

        // Required config values
        Config::set('AppDir', dirname(__DIR__) . '/data/email');
    }

    public function testCreate()
    {
        // Test returns send response
        $this->c->mailer
            ->expects($this->at(0))
            ->method('send')
            ->willReturn(true);

        $this->c->mailer
            ->expects($this->at(1))
            ->method('send')
            ->willReturn(false);

        $this->assertTrue(Email::create($this->c, 'george@webb.uno', 'George Webb', 'test_email'));
        $this->assertFalse(Email::create($this->c, 'george@webb.uno', 'George Webb', 'test_email'));
    }

    public function testPrepare()
    {
        $email = new Email($this->c, 'george@webb.uno', 'George Webb', 'test_email');
        $email->prepare();

        // Check expected values have been set on mailer

        // Config
        $this->assertEquals('smtp', $this->c->mailer->Mailer);
        $this->assertEquals('localhost', $this->c->mailer->Host);
        $this->assertEquals(false, $this->c->mailer->SMTPSecure);
        $this->assertEquals(25, $this->c->mailer->Port);
        $this->assertEquals(true, $this->c->mailer->SMTPAuth);
        $this->assertEquals('user1', $this->c->mailer->Username);
        $this->assertEquals('password1', $this->c->mailer->Password);

        // Addresses
        $this->assertEquals('from@example.com', $this->c->mailer->From);
        $this->assertEquals('John Sender', $this->c->mailer->FromName);
        $this->assertContains(array('George Webb' => 'george@webb.uno'), $this->c->mailer->getAllRecipientAddresses());
        $this->assertTrue(array_key_exists('from@example.com', $this->c->mailer->getReplyToAddresses()));

        // Content
        $this->assertEquals('Testing email sending', $this->c->mailer->Subject);
        $this->assertEquals('TEST EMAIL HTML', $this->c->mailer->Body);
    }

    public function testSend()
    {
        // Mock mailer response
        $this->c->mailer
            ->expects($this->at(0))
            ->method('send')
            ->willReturn(true);

        $this->c->mailer
            ->expects($this->at(1))
            ->method('send')
            ->willReturn(false);

        $email = new Email($this->c, 'george@webb.uno', 'George Webb', 'test_email');
        $email->prepare();

        $this->assertTrue($email->send());
        $this->assertFalse($email->send());

        // Check log message
        $test_handler = $this->c->logger->getHandlers()[0];
        $this->assertTrue($test_handler->hasRecordThatContains('Mailer Error: ', Logger::ERROR));
    }
}
