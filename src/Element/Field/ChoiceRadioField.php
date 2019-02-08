<?php
/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */

namespace Ingenerator\Form\Element\Field;


/**
 */
class ChoiceRadioField extends ChoiceField
{

    protected function getDefaultSchema()
    {
        $options = parent::getDefaultSchema();
        // Doesn't make sense as a common default for radio options
        $options['add_empty_choice'] = FALSE;

        return $options;
    }

}
