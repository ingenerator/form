<?php
/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */

namespace Ingenerator\Form\Element\Field;


use Ingenerator\Form\Util\FormDataArray;

/**
 *
 * @property-read int rows
 */
class TextareaField extends AbstractFormField
{

    protected function validateConstraintSchema(array $constraints)
    {
        // Temporarily, accept any constraints array
    }

    protected function getDefaultSchema()
    {
        $default         = parent::getDefaultSchema();
        $default['rows'] = NULL;

        return $default;
    }

    public function assignValue(FormDataArray $post)
    {
        parent::assignValue($post);
        if ($this->rows === NULL) {
            $text_lines           = count(explode("\n", $this->html_value));
            $this->schema['rows'] = max(2, $text_lines + 1);
        }

    }

}
