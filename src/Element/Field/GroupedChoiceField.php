<?php
/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */

namespace Ingenerator\Form\Element\Field;


/**
 * @property-read bool     add_empty_choice
 * @property-read array[]  choice_groups
 * @property-read string[] valid_values
 * @property-read bool     is_empty_selected
 * @property-read string   length
 */
class GroupedChoiceField extends AbstractFormField
{

    /**
     * @var array
     */
    protected $valid_values;

    public function __construct(array $schema)
    {
        parent::__construct($schema);
        $this->valid_values = $this->parseAndValidateChoiceGroups($schema);
    }

    /**
     * @param array $schema
     *
     * @return array
     */
    protected function parseAndValidateChoiceGroups(array $schema)
    {
        $values = [];
        foreach ($schema['choice_groups'] as $group) {
            $this->throwUnlessArrayWithKeys($group, ['group_caption', 'choices'], 'choice_groups');
            foreach ($group['choices'] as $index => $choice) {
                $this->throwUnlessArrayWithKeys(
                    $choice,
                    ['caption', 'value'],
                    'choice_groups.'.$index.'.choices'
                );
                $values[] = (string) $choice['value'];
            }
        }

        return $values;
    }

    /**
     * @param array  $array
     * @param array  $keys
     * @param string $field_path
     */
    protected function throwUnlessArrayWithKeys($array, array $keys, $field_path)
    {
        if ( ! is_array($array)) {
            throw new \InvalidArgumentException('`'.$field_path.'` should be an array`');
        }
        if ($missing_keys = array_diff($keys, array_keys($array))) {
            throw new \InvalidArgumentException(
                'Missing keys '.implode(', ', $missing_keys).' for '.$field_path
            );
        }
    }

    public function __get($option)
    {
        switch ($option) {
            case 'is_empty_selected':
                return $this->isEmptySelected();

            case 'choice_groups':
                return $this->getChoiceGroupsWithSelection();

            case 'valid_values':
                return $this->valid_values;

            default:
                return parent::__get($option);
        }
    }

    /**
     * @return bool
     */
    protected function isEmptySelected()
    {
        return ! in_array($this->html_value, $this->valid_values, TRUE);
    }

    protected function getChoiceGroupsWithSelection()
    {
        $groups = [];
        foreach ($this->schema['choice_groups'] as $group) {
            $group['choices'] = array_map(
                function (array $choice) {
                    $choice['value']    = (string) $choice['value'];
                    $is_selected        = ($choice['value'] === $this->html_value);
                    $choice['selected'] = $is_selected ? 'selected' : '';
                    if ( ! isset($choice['data'])) {
                        $choice['data'] = [];
                    }

                    return $choice;
                },
                $group['choices']
            );

            $groups[] = $group;
        }

        return $groups;
    }

    protected function validateConstraintSchema(array $constraints)
    {
        // Temporarily, accept any constraints array
    }

    protected function getDefaultSchema()
    {
        $options                     = parent::getDefaultSchema();
        $options['choice_groups']    = NULL;
        $options['length']           = NULL;
        $options['add_empty_choice'] = TRUE;

        return $options;
    }

    protected function listRequiredSchemaKeys()
    {
        return array_merge(parent::listRequiredSchemaKeys(), ['choice_groups']);
    }

    protected function buildChoiceOption(array $schema_option)
    {
        $schema_option['value']    = (string) $schema_option['value'];
        $schema_option['selected'] = '';
        $schema_option['disabled'] = '';

        return $schema_option;
    }

}
