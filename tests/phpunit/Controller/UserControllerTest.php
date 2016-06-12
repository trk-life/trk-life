<?php

namespace TrkLife\Controller;

use Monolog\Handler\TestHandler;
use Monolog\Logger;
use TrkLife\Container;
use TrkLife\Entity\User;
use TrkLife\TestUtilsTrait;

/**
 * Class UserControllerTest
 *
 * @package TrkLife\Email
 * @author George Webb <george@webb.uno>
 */
class UserControllerTest extends \PHPUnit_Framework_TestCase
{
    use TestUtilsTrait;

    /**
     * @var Container
     */
    public $c;

    /**
     * @var UserController
     */
    public $user_controller;

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
        $this->c['mailer'] = function () {
            $mailer = $this
                ->getMockBuilder('\PHPMailer')
                ->setMethods(
                    array('send')
                )
                ->getMock()
                ->method('send')
                ->willReturn(true);

            return $mailer;
        };

        // Mocked entity manager
        $this->c['EntityManager'] = function () {
            $em = $this
                ->getMockBuilder('\Doctrine\ORM\EntityManager')
                ->disableOriginalConstructor()
                ->setMethods(
                    array('getRepository', 'persist', 'flush', 'remove')
                )
                ->getMock();

            return $em;
        };

        $this->user_controller = new UserController($this->c);
    }

    public function testLoginRejectsInvalidEmailAndPassword()
    {
        // No password
        $request = $this->createRequest(false, array(), array(
            'email' => 'george@webb.uno'
        ));
        $response = $this->user_controller->login($request, $this->createResponse());
        $this->assertEquals('application/json;charset=utf-8', $response->getHeaderLine('content-type'));
        $this->assertEquals(
            '{"status":"fail","message":"Invalid email or password"}',
            $response->getBody()->__toString()
        );

        // Empty email
        $request = $this->createRequest(false, array(), array(
            'email' => '',
            'password' => '12345678'
        ));
        $response = $this->user_controller->login($request, $this->createResponse());
        $this->assertEquals(
            '{"status":"fail","message":"Invalid email or password"}',
            $response->getBody()->__toString()
        );
    }

    public function testLoginWithIncorrectEmail()
    {
        $request = $this->createRequest(false, array(), array(
            'email' => 'fake@example.com',
            'password' => 'password1'
        ));

        $user_repo = $this->createRepositoryMock();

        $user_repo
            ->expects($this->once())
            ->method('__call')
            ->with(
                $this->equalTo('findOneByEmail'),
                $this->equalTo(['fake@example.com'])
            )
            ->willReturn(null);

        $this->c->EntityManager
            ->expects($this->at(0))
            ->method('getRepository')
            ->willReturn($user_repo);

        $response = $this->user_controller->login($request, $this->createResponse());

        $this->assertEquals(
            '{"status":"fail","message":"A user with this email and password cannot be found."}',
            $response->getBody()->__toString()
        );
    }

    public function testLoginWithIncorrectPassword()
    {
        $request = $this->createRequest(false, array(), array(
            'email' => 'correct@example.com',
            'password' => 'incorrectpassword1'
        ));

        $user_repo = $this->createRepositoryMock();

        $user = new User();
        $user->set('email', 'correct@example.com');
        $user->set('password', 'correctpassword1');
        $user->set('status', User::STATUS_ACTIVE);

        $user_repo
            ->expects($this->once())
            ->method('__call')
            ->with(
                $this->equalTo('findOneByEmail'),
                $this->equalTo(['correct@example.com'])
            )
            ->willReturn($user);

        $this->c->EntityManager
            ->expects($this->at(0))
            ->method('getRepository')
            ->willReturn($user_repo);

        $response = $this->user_controller->login($request, $this->createResponse());

        $this->assertEquals(
            '{"status":"fail","message":"A user with this email and password cannot be found."}',
            $response->getBody()->__toString()
        );
    }

    public function testLoginWithNotActiveUser()
    {
        $request = $this->createRequest(false, array(), array(
            'email' => 'correct@example.com',
            'password' => 'correctpassword1'
        ));

        $user_repo = $this->createRepositoryMock();

        $user = new User();
        $user->set('email', 'correct@example.com');
        $user->set('password', 'correctpassword1');
        $user->set('status', User::STATUS_DISABLED);

        $user_repo
            ->expects($this->once())
            ->method('__call')
            ->with(
                $this->equalTo('findOneByEmail'),
                $this->equalTo(['correct@example.com'])
            )
            ->willReturn($user);

        $this->c->EntityManager
            ->expects($this->at(0))
            ->method('getRepository')
            ->willReturn($user_repo);

        $response = $this->user_controller->login($request, $this->createResponse());

        $this->assertEquals(
            '{"status":"fail","message":"This user is currently disabled."}',
            $response->getBody()->__toString()
        );
    }

    public function testLoginCorrectLogin()
    {
        $request = $this->createRequest(false, array(), array(
            'email' => 'correct@example.com',
            'password' => 'correctpassword1'
        ));

        $user_repo = $this->createRepositoryMock();

        $user = new User();
        $user->set('email', 'correct@example.com');
        $user->set('password', 'correctpassword1');
        $user->set('status', User::STATUS_ACTIVE);

        $user_repo
            ->expects($this->once())
            ->method('__call')
            ->with(
                $this->equalTo('findOneByEmail'),
                $this->equalTo(['correct@example.com'])
            )
            ->willReturn($user);

        $this->c->EntityManager
            ->expects($this->at(0))
            ->method('getRepository')
            ->willReturn($user_repo);

        $this->c->EntityManager
            ->expects($this->at(1))
            ->method('persist')
            ->will($this->returnCallback(function($token) use (&$token_grab) {
                $token_grab = $token;
            }));

        $response = $this->user_controller->login($request, $this->createResponse());

        // Assert token expires time is one day
        $this->assertEquals(
            60 * 60 * 24,
            $token_grab->get('expires_after')
        );

        $response_array = json_decode($response->getBody()->__toString(), true);

        $this->assertEquals('success', $response_array['status']);
        $this->assertArrayHasKey('user', $response_array);
    }

    public function testLoginStayLoggedIn()
    {
        $request = $this->createRequest(false, array(), array(
            'email' => 'correct@example.com',
            'password' => 'correctpassword1',
            'stay_logged_in' => 1
        ));

        $user_repo = $this->createRepositoryMock();

        $user = new User();
        $user->set('email', 'correct@example.com');
        $user->set('password', 'correctpassword1');
        $user->set('status', User::STATUS_ACTIVE);

        $user_repo
            ->expects($this->once())
            ->method('__call')
            ->with(
                $this->equalTo('findOneByEmail'),
                $this->equalTo(['correct@example.com'])
            )
            ->willReturn($user);

        $this->c->EntityManager
            ->expects($this->at(0))
            ->method('getRepository')
            ->willReturn($user_repo);

        $this->c->EntityManager
            ->expects($this->at(1))
            ->method('persist')
            ->will($this->returnCallback(function($token) use (&$token_grab) {
                $token_grab = $token;
            }));

        $this->user_controller->login($request, $this->createResponse());

        $this->assertEquals(
            60 * 60 * 24 * 90,
            $token_grab->get('expires_after')
        );
    }

    public function testValidateLogin()
    {
        $response = $this->user_controller->validateToken($this->createRequest(), $this->createResponse());

        $this->assertEquals('application/json;charset=utf-8', $response->getHeaderLine('content-type'));
        $this->assertEquals(
            '{"status":"success","message":"Token is valid","user":{"id":6,"email":"george@webb.uno",'
            . '"first_name":"George","last_name":"Webb","role":"admin","status":"active",'
            . '"created":1234567890,"modified":1234567890}}',
            $response->getBody()->__toString()
        );
    }

    public function testLogout()
    {
        // TODO
    }

    public function testForgottenPassword()
    {
        // TODO
    }

    public function testResetPassword()
    {
        // TODO
    }
}
