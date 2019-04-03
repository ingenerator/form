<?php
/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */

namespace Ingenerator\Form\Element;


use Ingenerator\Form\FormElementFactory;
use Ingenerator\Form\Util\FormDataArray;


/**
 *
 * @property-read string[]              container_data
 * @property-read AbstractFormElement[] fields
 * @property-read string                label
 */
class FormGroupElement extends AbstractFormElement implements FormValueElement
{
    public function __construct(array $schema, FormElementFactory $element_factory)
    {
        parent::__construct($schema);
        $this->schema['fields'] = $element_factory->make($schema['fields']);
    }

    public function assignValue(FormDataArray $data)
    {
        foreach ($this->fields as $field) {
            if ($field instanceof FormValueElement) {
                $field->assignValue($data);
            }
        }
    }

    public function collectValue(FormDataArray $data)
    {
        foreach ($this->fields as $field) {
            if ($field instanceof FormValueElement) {
                $field->collectValue($data);
            }
        }
    }

    public function assignErrors(FormDataArray $errors)
    {
        foreach ($this->fields as $field) {
            if ($field instanceof FormValueElement) {
                $field->assignErrors($errors);
            }
        }
    }

    protected function getDefaultSchema()
    {
        $defaults                   = parent::getDefaultSchema();
        $defaults['fields']         = NULL;
        $defaults['label']          = NULL;
        $defaults['container_data'] = [];

        return $defaults;
    }

    protected function listRequiredSchemaKeys()
    {
        return \array_merge(parent::listRequiredSchemaKeys(), ['fields', 'label']);
    }

}
