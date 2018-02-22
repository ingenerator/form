<?php
/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */

namespace Ingenerator\Form\Renderer;


use Ingenerator\Form\Element\AbstractFormElement;
use Ingenerator\Form\Element\Field\AbstractFormField;
use Ingenerator\Form\Form;

class FormElementRenderer
{
    /**
     * @var array
     */
    protected $view_map;

    public function __construct(array $element_type_map)
    {
        $this->view_map = array_flip($element_type_map);
    }

    /**
     * @param \Ingenerator\Form\Element\AbstractFormElement $element
     *
     * @return string
     */
    public function render(AbstractFormElement $element)
    {
        if ($element instanceof Form) {
            $output = '';
            foreach ($element->elements as $child) {
                $output .= $this->renderElement($child);
            }

            return $output;
        } else {
            return $this->renderElement($element);
        }
    }

    /**
     * @param \Ingenerator\Form\Element\Field\AbstractFormField $field
     *
     * @return string
     */
    public function renderConstraintsAttributes(AbstractFormField $field)
    {
        return \HTML::attributes($field->constraints);
    }

    /**
     * @param \Ingenerator\Form\Element\AbstractFormElement $element
     *
     * @return string
     */
    protected function renderElement(AbstractFormElement $element)
    {
        if ( ! $view_name = \Arr::get($this->view_map, get_class($element))) {
            throw new \OutOfBoundsException('No mapped view for form element '.get_class($element));
        }

        return \View::factory(
            'form_fields/edit/'.$view_name,
            ['field' => $element, 'form_renderer' => $this]
        )->render();
    }
}
