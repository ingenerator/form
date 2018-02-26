<?php
/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */

namespace Ingenerator\Form;

use Ingenerator\Form\Element\AbstractFormElement;

class FormElementFactory
{

    /**
     * @var \Ingenerator\Form\FormConfig
     */
    protected $config;

    /**
     * @param \Ingenerator\Form\FormConfig $config
     */
    public function __construct(FormConfig $config)
    {
        $this->config = $config;
    }

    /**
     * @param array $elements_schema
     *
     * @return AbstractFormElement[]
     */
    public function make(array $elements_schema)
    {
        $elements = [];
        foreach ($elements_schema as $element) {

            $elements[] = $this->makeElement($element);
        }

        return $elements;
    }

    /**
     * @param array $element_schema
     *
     * @return AbstractFormElement
     */
    protected function makeElement(array $element_schema)
    {
        if ( ! $type = \Arr::get($element_schema, 'type')) {
            throw new \InvalidArgumentException(
                "No `type` defined in form element\n".json_encode($element_schema)
            );
        }

        if ( ! $class = $this->config->getElementClass($type)) {
            throw UndefinedFieldTypeException::withType($type);
        }

        return new $class($element_schema, $this);
    }
}
