<?php

namespace TrkLife\Exception;

use Exception;

/**
 * Class ValidationException
 *
 * Exception for validation errors
 *
 * @package TrkLife\Exception
 */
class ValidationException extends Exception
{
    /**
     * List of validation messages
     *
     * @var array
     */
    public $validation_messages;

    /**
     * ValidationException constructor.
     *
     * @param array $validation_messages    List of validation messages
     * @codeCoverageIgnore
     */
    public function __construct($validation_messages = array())
    {
        $this->validation_messages = $validation_messages;
    }
}
