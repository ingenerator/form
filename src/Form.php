<?php
/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */
namespace Ingenerator\Form;

use Ingenerator\Form\Element\AbstractFormElement;
use Ingenerator\Form\Element\FormValueElement;
use Ingenerator\Form\Util\FormDataArray;

/**
 * @property-read AbstractFormElement[] elements
 * @property-read boolean               has_errors
 */
class Form extends AbstractFormElement
{

    public function __construct(array $schema, FormElementFactory $element_factory)
    {
        parent::__construct($schema);
        $this->schema['elements']   = $element_factory->make($schema['elements']);
        $this->schema['has_errors'] = FALSE;
    }

    /**
     * @return array
     */
    public function getValues()
    {
        $data = new FormDataArray([]);
        foreach ($this->elements as $element) {
            if ($element instanceof FormValueElement) {
                $element->collectValue($data);
            }
        }

        return $data->getValues();
    }

    /**
     * @param array $data
     */
    public function setValues(array $data)
    {
        $data = new Util\FormDataArray($data);
        foreach ($this->elements as $element) {
            if ($element instanceof FormValueElement) {
                $element->assignValue($data);
            }
        }
    }

    /**
     * @param array $errors
     */
    public function setErrors(array $errors)
    {
        $this->schema['has_errors'] = ! empty($errors);
        $errors = new Util\FormDataArray($errors);
        foreach ($this->elements as $element) {
            if ($element instanceof FormValueElement) {
                $element->assignErrors($errors);
            }
        }
    }

    protected function getDefaultSchema()
    {
        $defaults             = parent::getDefaultSchema();
        $defaults['elements'] = NULL;

        return $defaults;
    }

    protected function listRequiredSchemaKeys()
    {
        return array_merge(parent::listRequiredSchemaKeys(), ['elements']);
    }

}
