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
     * @var string[]
     */
    protected $element_type_map;

    /**
     * @param string[] $element_type_map
     */
    public function __construct(array $element_type_map)
    {
        $this->element_type_map = $element_type_map;
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

        if ( ! $class = \Arr::get($this->element_type_map, $type)) {
            throw new \OutOfBoundsException("Undefined form element type $type");
        }

        return new $class($element_schema, $this);
    }
}
