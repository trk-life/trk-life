<?php

namespace TrkLife\Email;

use Interop\Container\ContainerInterface;
use PHPMailer;
use TrkLife\Config;

/**
 * Class Email
 *
 * @package TrkLife\Email
 * @author George Webb <george@webb.uno>
 */
class Email
{
    /**
     * DI container
     *
     * @var ContainerInterface
     */
    private $c;

    /**
     * Instance of PHPMailer
     *
     * @var PHPMailer
     */
    private $mailer;

    /**
     * The email address of the recipient
     *
     * @var string
     */
    private $to_email;

    /**
     * The name of the email recipient
     *
     * @var string
     */
    private $to_name;

    /**
     * The email address of the sender
     *
     * @var string
     */
    private $from_email;

    /**
     * The name of the email sender
     *
     * @var string
     */
    private $from_name;

    /**
     * The email type - properties defined in config
     *
     * @var string
     */
    private $email_type;

    /**
     * Data to use in template
     *
     * @var array
     */
    private $data;

    /**
     * Email constructor.
     *
     * @param ContainerInterface $c DI container
     * @param string $to_email      The recipients email address
     * @param string $to_name       The recipients name
     * @param string $email_type    The email type - definied in the config
     * @param array $data           The data to use in the template
     */
    public function __construct($c, $to_email, $to_name, $email_type, $data = array())
    {
        $this->c = $c;

        $this->mailer = new PHPMailer();

        $this->mailer->isSMTP();
        $this->mailer->Host = Config::get('Email.smtp.host');
        $this->mailer->SMTPSecure = Config::get('Email.smtp.tls_or_ssl');
        $this->mailer->Port = Config::get('Email.smtp.port');

        if (Config::get('Email.smtp.use_auth')) {
            $this->mailer->SMTPAuth = true;
            $this->mailer->Username = Config::get('Email.smtp.username');
            $this->mailer->Password = Config::get('Email.smtp.password');
        }

        $this->to_email = $to_email;
        $this->to_name = $to_name;
        $this->from_email = Config::get('Email.from_address');
        $this->from_name = Config::get('Email.from_name');
        $this->email_type = $email_type;
        $this->data = $data;
    }

    /**
     * Convenience function for creating and sending an email
     *
     * @param ContainerInterface $c DI container
     * @param string $to_email      The recipients email address
     * @param string $to_name       The recipients name
     * @param string $email_type    The email type - definied in the config
     * @param array $data           The data to use in the template
     */
    public static function create($c, $to_email, $to_name, $email_type, $data = array())
    {
        $email = new static($c, $to_email, $to_name, $email_type, $data);
        $email->prepare();
        return $email->send();
    }

    /**
     * Prepare an email for sending
     *
     * Sets email headers, build from template, etc.
     */
    public function prepare()
    {
        $this->mailer->setFrom($this->from_email, $this->from_name);
        $this->mailer->addReplyTo($this->from_email, $this->from_name);
        $this->mailer->addAddress($this->to_email, $this->to_name);
        $this->mailer->isHTML(true);

        $email_builder = new EmailBuilder($this->c, $this->email_type, $this->data);

        $this->mailer->Subject = Config::get("Email.types.{$this->email_type}.subject");
        $this->mailer->Body    = $email_builder->buildHtml();
        $this->mailer->AltBody = $email_builder->buildText();
    }

    /**
     * Does the actual email sending
     *
     * @return bool The success or failure of sending
     */
    public function send()
    {
        if(!$this->mailer->send()) {
            $this->c->logger->addError('Mailer Error: ' . $this->mailer->ErrorInfo);
            return false;
        }
        return true;
    }
}
