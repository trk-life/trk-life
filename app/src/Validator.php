<?php

namespace TrkLife;

use Respect\Validation\Validator as v;

/**
 * Class Validator
 *
 * @package TrkLife
 * @author George Webb <george@webb.uno>
 */
class Validator
{
    /**
     * Validate a field with its given type and rules
     *
     * @param mixed $value  The value to validate
     * @param string $type  The type of the value
     * @param array $rules  The validation rules to use
     * @return bool         Whether or not field validated successfully
     */
    public static function validateField($value, $type, $rules = array())
    {
        // Validate required
        $required = empty($rules['required']) ? false : $rules['required'];
        if ($value === null) {
            return !$required;
        }

        // Validate type
        $validator = v::$type();

        // Validate rules
        foreach ($rules as $rule => $options) {
            // We've already dealt with required
            if ($rule == 'required') {
                continue;
            }

            call_user_func_array(array($validator, $rule), $options);
        }

        if (!$validator->validate($value)) {
            return false;
        }

        return true;
    }
}
