<?php
/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */

namespace Ingenerator\Form;


class UndefinedFieldTypeException extends \OutOfBoundsException
{

    /**
     * @param string $type
     *
     * @return static
     */
    public static function withType($type)
    {
        return new static("Undefined form element type $type");
    }

}
