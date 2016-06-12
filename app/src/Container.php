<?php

namespace TrkLife;

use Doctrine\ORM\EntityManager;
use Monolog\Logger;
use PHPMailer;

/**
 * Overridden container for defining properties for IDE
 *
 * @property-read EntityManager EntityManager
 * @property-read Logger        logger
 * @property-read PHPMailer     mailer
 *
 * @package TrkLife\Entity
 * @author George Webb <george@webb.uno>
 */
class Container extends \Slim\Container
{

}
