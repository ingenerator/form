<?php
/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */

namespace Ingenerator\Form\Element\Field;


use Ingenerator\Form\Element\AbstractFormElement;
use Ingenerator\Form\Element\FormValueElement;
use Ingenerator\Form\Util\FormDataArray;

/**
 *
 *
 * @property-read string   id
 * @property-read string   label
 * @property-read array    constraints
 * @property-read array    container_data
 * @property-read string   display_label
 * @property-read string   name
 * @property-read string   help_text
 * @property-read string   empty_value
 * @property-read string   html_value
 * @property-read string[] errors
 */
abstract class AbstractFormField extends AbstractFormElement implements FormValueElement
{
    protected $errors = [];
    protected $html_value = '';

    public function __construct(array $schema)
    {
        unset($schema['hide_display_if']);
        unset($schema['highlight_if']);
        if ( ! \Arr::get($schema, 'display_label')) {
            $schema['display_label'] = \Arr::get($schema, 'label');
        }

        parent::__construct($schema);

        if ( ! is_array($this->schema['constraints'])) {
            throw new \InvalidArgumentException('Field constraints must be an array');
        } else {
            $this->validateConstraintSchema($this->schema['constraints']);
        }
    }

    protected function validateConstraintSchema(array $constraints)
    {
        if ( ! empty($constraints)) {
            throw new \InvalidArgumentException(get_class($this).' does not support `constraints`');
        }
    }

    public function __get($option)
    {
        if ($option === 'html_value') {
            return $this->html_value;
        } elseif ($option === 'errors') {
            return $this->errors;
        }

        return parent::__get($option);
    }

    public function assignValue(FormDataArray $post)
    {
        $value            = $post->getRawValue($this->name);
        $this->html_value = (string) $value;
    }

    public function collectValue(FormDataArray $data)
    {
        $value = $this->html_value === '' ? NULL : $this->html_value;
        $data->setFieldValue($this->name, $value);
    }

    public function assignErrors(FormDataArray $errors)
    {
        $this->errors = (array) $errors->getRawValue($this->name) ?: [];
    }

    protected function getDefaultSchema()
    {
        return [
            'id'             => uniqid('field'),
            'label'          => NULL,
            'display_label'  => NULL,
            'help_text'      => NULL,
            'name'           => NULL,
            'empty_value'    => NULL,
            'constraints'    => [],
            'container_data' => []
        ];
    }

    protected function listRequiredSchemaKeys()
    {
        return ['label', 'name'];
    }

}
