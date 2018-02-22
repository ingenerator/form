<?php
/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */
namespace Ingenerator\Form\Element\Field;

/**
 * @property-read string length
 * @property-read string text_type
 */
class TextField extends AbstractFormField
{
    protected function getDefaultSchema()
    {
        $default              = parent::getDefaultSchema();
        $default['length']    = NULL;
        $default['text_type'] = 'text';

        return $default;
    }

    protected function validateConstraintSchema(array $constraints)
    {
        // Temporarily, accept any constraints array
    }

}
