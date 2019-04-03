<?php
/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */

namespace Ingenerator\Form\Element\Field;


use Ingenerator\Form\Element\FormValueElement;
use Ingenerator\Form\FormElementFactory;
use Ingenerator\Form\Util\FormDataArray;

/**
 *
 *
 * @property-read array[] groups
 */
class RepeatingGroupField extends AbstractFormField
{
    /**
     * @var FormElementFactory
     */
    protected $element_factory;

    /**
     * @var array[]
     */
    protected $groups = [];

    public function __construct(array $schema, FormElementFactory $element_factory)
    {
        parent::__construct($schema);
        $this->element_factory = $element_factory;
        $this->groups          = [$this->buildGroupFields(0)];
    }

    /**
     * @param $group_index
     *
     * @return \Ingenerator\Form\Element\AbstractFormElement[]
     */
    protected function buildGroupFields($group_index)
    {
        $group_fields = [];
        foreach ($this->schema['fields'] as $field_schema) {
            if ($fieldname = \Arr::get($field_schema, 'name')) {
                if ( ! \preg_match('/^\[.+?\]$/', $fieldname)) {
                    throw new \InvalidArgumentException(
                        "Invalid fieldname `$fieldname` - did you mean `[$fieldname]`"
                    );
                }

                $field_schema['name'] = \sprintf('%s[%s]%s', $this->name, $group_index, $fieldname);
            }

            $group_fields[] = $field_schema;
        }
        $group = $this->element_factory->make($group_fields);

        return $group;
    }

    public function __get($option)
    {
        if ($option === 'groups') {
            return $this->groups;
        }

        return parent::__get($option);
    }

    public function assignValue(FormDataArray $post)
    {
        $values       = $post->getRawValue($this->name) ?: [];
        $this->groups = [];
        foreach (\array_keys($values) ?: [0] as $group_index) {
            $group = $this->buildGroupFields($group_index);
            foreach ($group as $field) {
                if ($field instanceof FormValueElement) {
                    $field->assignValue($post);
                }
            }

            $this->groups[] = $group;
        }
    }

    public function collectValue(FormDataArray $data)
    {
        foreach ($this->groups as $group) {
            foreach ($group as $field) {
                if ($field instanceof FormValueElement) {
                    $field->collectValue($data);
                }
            }
        }
    }

    protected function getDefaultSchema()
    {
        $default           = parent::getDefaultSchema();
        $default['fields'] = NULL;

        return $default;
    }

    protected function listRequiredSchemaKeys()
    {
        return \array_merge(parent::listRequiredSchemaKeys(), ['fields']);
    }


}
