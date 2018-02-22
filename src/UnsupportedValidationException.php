<?php
/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */

namespace Ingenerator\Form;


use Ingenerator\Form\Element\AbstractFormElement;
use Ingenerator\Form\Element\Field\TextField;

class UnsupportedValidationException extends \InvalidArgumentException
{

    public static function badClass(AbstractFormElement $element)
    {
        return new static(
            'Cannot automatically validate element of type '.get_class($element)
        );
    }

    public static function badTextType(TextField $field)
    {
        return new static(
            'Cannot automatically validate text field with text_type=`'.$field->text_type.'`'
        );

    }

    public static function badConstraint(AbstractFormElement $field, $constraint)
    {
        return new static(
            'Cannot automatically validate `'.$constraint.'` constraint for '.get_class($field)
        );
    }

}
