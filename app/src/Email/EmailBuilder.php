<?php

namespace TrkLife\Email;
use Interop\Container\ContainerInterface;
use League\Plates\Engine;
use TrkLife\Config;

/**
 * Class EmailBuilder
 *
 * @package TrkLife\Email
 * @author George Webb <george@webb.uno>
 */
class EmailBuilder
{
    /**
     * The template directory, relative to the project root
     */
    const TEMPLATES_PATH = '/email_templates/';

    /**
     * DI container
     *
     * @var ContainerInterface
     */
    private $c;

    /**
     * The email type - used to get template path
     *
     * @var string
     */
    private $email_type;

    /**
     * The data to use in the template
     *
     * @var array
     */
    private $data;

    /**
     * The plates rendering engine
     *
     * @var Engine
     */
    private $engine;

    /**
     * EmailBuilder constructor.
     *
     * @param ContainerInterface $c DI container
     * @param string $email_type    The email type - used to get template path
     * @param array $data           The data to use in the template
     */
    public function __construct($c, $email_type, $data)
    {
        $this->c = $c;
        $this->email_type = $email_type;
        $this->data = $data;
        $this->engine = new Engine(Config::get('AppDir') . static::TEMPLATES_PATH);
    }

    /**
     * Build the html email and returns
     *
     * @return string   The rendered email
     */
    public function buildHtml()
    {
        return $this->build('html');
    }

    /**
     * Build the text email and returns
     *
     * @return string   The rendered email
     */
    public function buildText()
    {
        return $this->build('text');
    }

    /**
     * Builds email of given format (html, text)
     *
     * @param string $format    The email format - html or text
     * @return string           The rendered email
     */
    private function build($format)
    {
        return $this->engine->render($this->email_type . '_' . $format, $this->data);
    }
}
