<?php
/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */

namespace Ingenerator\Form\Renderer;


use Ingenerator\Form\Element\AbstractFormElement;

class UndefinedTemplateException extends \OutOfBoundsException
{
    public static function forElement(AbstractFormElement $element, $mode)
    {
        return new static('No mapped `'.$mode.'` template for form element '.\get_class($element));
    }
}
