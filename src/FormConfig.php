<?php
/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */

namespace Ingenerator\Form;

use Ingenerator\Form\Element\BodyTextFormElement;
use Ingenerator\Form\Element\Field\ChoiceField;
use Ingenerator\Form\Element\Field\ChoiceOrOtherField;
use Ingenerator\Form\Element\Field\ChoiceRadioField;
use Ingenerator\Form\Element\Field\DateField;
use Ingenerator\Form\Element\Field\GroupedChoiceField;
use Ingenerator\Form\Element\Field\RepeatingGroupField;
use Ingenerator\Form\Element\Field\RoughDateRangeField;
use Ingenerator\Form\Element\Field\TextareaField;
use Ingenerator\Form\Element\Field\TextField;
use Ingenerator\Form\Element\FormGroupElement;

class FormConfig
{
    protected $config;

    /**
     * @param array $config
     *
     * @see \Ingenerator\Form\FormConfig::withDefaults() for normal use
     */
    public function __construct(array $config)
    {
        if ($missing = array_diff(['element_type_map', 'template_map'], array_keys($config))) {
            throw InvalidFormConfigException::missingProperties($missing);
        }
        $this->config = $config;
    }

    /**
     * Create a config with the standard shipped fields and templates.
     *
     * @param array $override any custom config to override the default element types or templates
     *
     * @return static
     */
    public static function withDefaults(array $override = NULL)
    {
        $tpl_dir = realpath(__DIR__.'/../field_templates/default');

        $config = \Arr::merge(
            [
                'element_type_map' => [
                    'body-text'        => BodyTextFormElement::class,
                    'choice'           => ChoiceField::class,
                    'choice-radio'     => ChoiceRadioField::class,
                    'choice-or-other'  => ChoiceOrOtherField::class,
                    'date'             => DateField::class,
                    'group'            => FormGroupElement::class,
                    'grouped-choice'   => GroupedChoiceField::class,
                    'repeating-group'  => RepeatingGroupField::class,
                    'rough-date-range' => RoughDateRangeField::class,
                    'text'             => TextField::class,
                    'textarea'         => TextareaField::class,
                ],
                'template_map'     => [
                    BodyTextFormElement::class => [
                        'edit'    => $tpl_dir.'/edit/body-text.php',
                        'display' => $tpl_dir.'/display/body-text.php',
                    ],
                    ChoiceField::class         => [
                        'edit'    => $tpl_dir.'/edit/choice.php',
                        'display' => $tpl_dir.'/display/text.php'
                    ],
                    ChoiceRadioField::class    => [
                        'edit'    => $tpl_dir.'/edit/choice-radio.php',
                        'display' => $tpl_dir.'/display/text.php'
                    ],
                    ChoiceOrOtherField::class  => [
                        'edit'    => $tpl_dir.'/edit/choice-or-other.php',
                        'display' => $tpl_dir.'/display/choice-or-other.php',
                    ],
                    DateField::class           => [
                        'edit' => $tpl_dir.'/edit/date.php',
                    ],
                    Form::class                => [
                        'edit'    => $tpl_dir.'/edit/form.php',
                        'display' => $tpl_dir.'/display/form.php',
                    ],
                    FormGroupElement::class    => [
                        'edit' => $tpl_dir.'/edit/group.php',
                    ],
                    GroupedChoiceField::class  => [
                        'edit' => $tpl_dir.'/edit/grouped-choice.php',
                    ],
                    RepeatingGroupField::class => [
                        'edit'    => $tpl_dir.'/edit/repeating-group.php',
                        'display' => $tpl_dir.'/display/repeating-group.php',
                    ],
                    RoughDateRangeField::class => [
                        'edit'    => $tpl_dir.'/edit/rough-date-range.php',
                        'display' => $tpl_dir.'/display/rough-date-range.php',
                    ],
                    TextField::class           => [
                        'edit'    => $tpl_dir.'/edit/text.php',
                        'display' => $tpl_dir.'/display/text.php',
                    ],
                    TextareaField::class       => [
                        'edit'    => $tpl_dir.'/edit/textarea.php',
                        'display' => $tpl_dir.'/display/text.php',
                    ],
                ],
            ],
            $override ?: []
        );

        return new static($config);
    }

    /**
     * @param string $type
     *
     * @return string|null
     */
    public function getElementClass($type)
    {
        return \Arr::get($this->config['element_type_map'], $type);
    }

    /**
     * @param string $element_class
     * @param string $mode
     *
     * @return string|null
     */
    public function getTemplateFile($element_class, $mode)
    {
        $templates = \Arr::get($this->config['template_map'], $element_class, []);

        return \Arr::get($templates, $mode);
    }

    /**
     * @return string[]
     */
    public function listDefinedElementTypes()
    {
        return array_keys(array_filter($this->config['element_type_map']));
    }

    public function validate()
    {
        $errors = array_merge(
            $this->validateElementClassesExist(),
            $this->validateTemplatesExist()
        );
        if ($errors) {
            throw InvalidFormConfigException::withErrors($errors);
        }

    }

    /**
     *
     * @return string[]
     */
    protected function validateElementClassesExist()
    {
        $errors = [];
        foreach ($this->config['element_type_map'] as $field => $class) {
            if ( ! class_exists($class)) {
                $errors[] = sprintf('No class `%s` for field type `%s`', $class, $field);
            }
        }

        return $errors;
    }

    /**
     * @return string[]
     */
    protected function validateTemplatesExist()
    {
        $errors = [];
        foreach ($this->config['template_map'] as $field => $templates) {
            foreach (array_filter($templates) as $mode => $template) {
                if ( ! (is_file($template) AND is_readable($template))) {
                    $errors[] = sprintf(
                        'Template `%s` for `%s` in `%s` mode is not a readable file',
                        $template,
                        $field,
                        $mode
                    );
                }
            }
        }

        return $errors;
    }
}
