<?php
/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */
namespace Ingenerator\Form\Element\Field;

use Ingenerator\Form\FormElementFactory;
use Ingenerator\Form\Util\FormDataArray;

/**
 *
 *
 * @property-read ChoiceField choice_field
 * @property-read TextField   detail_field
 * @property-read string      length
 * @property-read string[]    other_for_values
 */
class ChoiceOrOtherField extends AbstractFormField
{
    /**
     * @var ChoiceField
     */
    protected $choice_field;

    /**
     * @var TextField
     */
    protected $detail_field;

    public function __construct(array $schema, FormElementFactory $element_factory)
    {
        parent::__construct($schema);
        list($this->choice_field, $this->detail_field) = $element_factory->make(
            [
                [
                    'type'    => 'choice',
                    'name'    => $this->name.'[choice]',
                    'label'   => $this->label,
                    'length'  => $this->length,
                    'choices' => $schema['choices']
                ],
                [
                    'type'   => 'text',
                    'name'   => $this->name.'[detail]',
                    'label'  => $this->label.' (Other)',
                    'length' => $this->length
                ]
            ]
        );
    }

    public function __get($option)
    {
        if ($option === 'choice_field') {
            return $this->choice_field;
        } elseif ($option === 'detail_field') {
            return $this->detail_field;
        }

        return parent::__get($option);
    }

    public function assignValue(FormDataArray $post)
    {
        $this->choice_field->assignValue($post);
        $this->detail_field->assignValue($post);
    }

    public function collectValue(FormDataArray $data)
    {
        $this->choice_field->collectValue($data);
        $choice_value = $data->getRawValue($this->choice_field->name);
        // Only assign the detail field value if the choice is an "other" choice - in case user
        // has changed since they assigned it and haven't seen the value in the now-hidden detail
        if (in_array($choice_value, $this->other_for_values)) {
            $this->detail_field->collectValue($data);
        } else {
            $data->setFieldValue($this->detail_field->name, NULL);
        }
    }

    protected function getDefaultSchema()
    {
        $default                     = parent::getDefaultSchema();
        $default['length']           = NULL;
        $default['choices']          = NULL;
        $default['other_for_values'] = NULL;

        return $default;
    }

    protected function listRequiredSchemaKeys()
    {
        return array_merge(
            parent::listRequiredSchemaKeys(),
            ['choices', 'other_for_values']
        );
    }

}
