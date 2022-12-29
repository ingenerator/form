<?php
/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */

namespace Ingenerator\Form\Element\Field;


/**
 * @property-read bool $bordered_choices Whether to show borders around choices in the list (good for large captions)
 */
class ChoiceRadioField extends ChoiceField
{

    protected function getDefaultSchema()
    {
        $options = parent::getDefaultSchema();
        // Doesn't make sense as a common default for radio options
        $options['add_empty_choice'] = FALSE;
        $options['bordered_choices'] = FALSE;

        return $options;
    }

}
