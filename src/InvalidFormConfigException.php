<?php
/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */

namespace Ingenerator\Form;


class InvalidFormConfigException extends \InvalidArgumentException
{

    /**
     * @param array $missing
     *
     * @return static
     */
    public static function missingProperties(array $missing)
    {
        return new static(
            'FormConfig is missing required options - '.json_encode($missing)
        );
    }

    /**
     * @param string[] $errors
     *
     * @return static
     */
    public static function withErrors(array $errors)
    {
        return new static(
            'FormConfig is not valid: '."\n -".implode("\n -", $errors)
        );
    }

}
