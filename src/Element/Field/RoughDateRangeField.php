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
 * @property-read TextField from_field
 * @property-read TextField to_field
 */
class RoughDateRangeField extends AbstractFormField
{
    /**
     * @var \Ingenerator\Form\Element\Field\TextField
     */
    protected $from_field;

    /**
     * @var \Ingenerator\Form\Element\Field\TextField
     */
    protected $to_field;

    public function __construct(array $schema, FormElementFactory $element_factory)
    {
        parent::__construct($schema);
        list ($this->from_field, $this->to_field) = $element_factory->make(
            [
                ['type' => 'text', 'label' => 'Date From', 'name' => $this->name.'[from]'],
                ['type' => 'text', 'label' => 'Date To', 'name' => $this->name.'[to]']
            ]
        );
    }

    public function __get($option)
    {
        if ($option === 'display_value') {
            return $this->getDisplayValue();
        } elseif ($option === 'from_field') {
            return $this->from_field;
        } elseif ($option === 'to_field') {
            return $this->to_field;
        }

        return parent::__get($option);
    }

    protected function getDisplayValue()
    {
        $values = \array_filter(
            [
                $this->from_field->display_value,
                $this->to_field->display_value
            ]
        );
        
        if ($values) {
            return \implode(' - ', $values);
        } else {
            return $this->empty_value;
        }
    }

    public function assignValue(FormDataArray $post)
    {
        $this->from_field->assignValue($post);
        $this->to_field->assignValue($post);
    }

    public function collectValue(FormDataArray $data)
    {
        $this->from_field->collectValue($data);
        $this->to_field->collectValue($data);
    }

    protected function listRequiredSchemaKeys()
    {
        // label is not required for this field type
        return ['name'];
    }

}
