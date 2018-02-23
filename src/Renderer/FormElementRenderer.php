<?php
/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */

namespace Ingenerator\Form\Renderer;


use Ingenerator\Form\Criteria\FieldCriteriaMatcher;
use Ingenerator\Form\Element\AbstractFormElement;
use Ingenerator\Form\Element\Field\AbstractFormField;
use Ingenerator\Form\Form;
use Ingenerator\Form\FormConfig;

class FormElementRenderer
{

    /**
     * @var string
     */
    protected $render_mode;

    /**
     * @var FormConfig
     */
    protected $template_map;

    /**
     * @param \Ingenerator\Form\FormConfig $config
     */
    public function __construct(FormConfig $config, $render_mode = 'edit')
    {
        $this->config      = $config;
        $this->render_mode = $render_mode;
    }

    /**
     * @param string            $value
     * @param AbstractFormField $field
     *
     * @return string
     */
    public function getHighlightClasses($value, AbstractFormField $field)
    {
        $matcher = new FieldCriteriaMatcher;
        $classes = array_keys(
            array_filter(
                [
                    'answer-highlighted'    => $matcher->matches($value, $field->highlight_if),
                    'answer-display-hidden' => $matcher->matches($value, $field->hide_display_if),
                    'answer-empty'          => $matcher->matches($value, ['empty'])
                ]
            )
        );

        return implode(' ', $classes);
    }

    /**
     * @param \Ingenerator\Form\Element\AbstractFormElement $element
     *
     * @return string
     */
    public function render(AbstractFormElement $element)
    {
        if ( ! $template_file = $this->findTemplateForElement($element, $this->render_mode)) {
            throw UndefinedTemplateException::forElement($element, $this->render_mode);
        }


        return $this->requireWithAnonymousScope($template_file, $element);
    }

    /**
     * @param \Ingenerator\Form\Element\AbstractFormElement $element
     * @param string                                        $mode
     *
     * @return string
     */
    protected function findTemplateForElement(AbstractFormElement $element, $mode)
    {
        if ($template_file = $this->config->getTemplateFile(get_class($element), $mode)) {
            return $template_file;
        }

        if ($element instanceof Form) {
            return $this->config->getTemplateFile(Form::class, $mode);
        }

        return NULL;
    }

    /**
     * @param string              $template_file
     * @param AbstractFormElement $element
     *
     * @return string
     */
    protected function requireWithAnonymousScope($template_file, AbstractFormElement $element)
    {
        // Create a function with no scope except the variables it gets passed
        $bound_capture = function (
            AbstractFormElement $field,
            FormElementRenderer $form_renderer,
            $template_file
        ) {
            require $template_file;
        };
        $anon_capture  = $bound_capture->bindTo(NULL);

        // Render the template
        ob_start();
        try {
            $anon_capture($element, $this, $template_file);
        } finally {
            $output = ob_get_clean();
        }

        return $output;
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
}
