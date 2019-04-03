<?php
/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */

namespace Ingenerator\Form\Element\Field;


/**
 * @property-read array                  choices
 * @property-read string                 length
 * @property array|mixed|string|string[] valid_values
 */
class ChoiceField extends AbstractFormField
{
    /**
     * @var array
     */
    protected $choice_list = [];

    public function __construct(array $schema)
    {
        parent::__construct($schema);
        $this->choice_list = $this->parseAndValidateChoicesList($schema);
    }

    /**
     * @param array $schema
     *
     * @return array
     */
    protected function parseAndValidateChoicesList(array $schema)
    {
        $choices = [];
        foreach ($schema['choices'] as $choice) {
            if (\is_scalar($choice)) {
                $choices[] = [
                    'value'   => (string) $choice,
                    'caption' => (string) $choice
                ];
            } elseif ($this->isValidArrayChoiceDefinition($choice)) {
                $choice['value']   = (string) $choice['value'];
                $choice['caption'] = (string) $choice['caption'];
                $choices[]         = $choice;
            } else {
                throw new \InvalidArgumentException('Invalid choice definition for '.__CLASS__);
            }
        }

        return $choices;
    }

    /**
     * @param $choice
     *
     * @return bool
     */
    protected function isValidArrayChoiceDefinition($choice)
    {
        return (
            \is_array($choice)
            AND \array_key_exists('value', $choice)
            AND \array_key_exists('caption', $choice)
        );
    }

    public function __get($option)
    {
        switch ($option) {
            case 'choices':
                return $this->buildChoicesList();
            case 'display_value':
                return $this->getDisplayValue();
            case 'valid_values':
                return $this->listValidValues();
            default:
                return parent::__get($option);
        }
    }

    protected function buildChoicesList()
    {
        $choices       = [];
        $has_selection = FALSE;
        $has_empty     = FALSE;

        foreach ($this->choice_list as $choice) {
            $selected      = ($choice['value'] === $this->html_value) ? 'selected' : '';
            $has_selection = ($has_selection || $selected);
            if ($choice['value'] == '') {
                $has_empty = TRUE;
            }
            $choices[] = [
                'value'    => $choice['value'],
                'caption'  => $choice['caption'],
                'selected' => $selected,
                'disabled' => ''
            ];
        }

        if ($this->schema['add_empty_choice'] AND ! $has_empty) {
            \array_unshift(
                $choices,
                [
                    'value'    => '',
                    'caption'  => $this->empty_value,
                    'selected' => $has_selection ? '' : 'selected',
                    'disabled' => 'disabled'
                ]
            );
        }

        return $choices;
    }

    /**
     * @return string
     */
    protected function getDisplayValue()
    {
        $html_val = $this->html_value;
        foreach ($this->choice_list as $choice) {
            if ($choice['value'] === $this->html_value) {
                return $choice['caption'];
            }
        }

        return $this->empty_value;
    }

    protected function validateConstraintSchema(array $constraints)
    {
        // Temporarily, accept any constraints array
    }

    protected function getDefaultSchema()
    {
        $options                     = parent::getDefaultSchema();
        $options['choices']          = NULL;
        $options['length']           = NULL;
        $options['add_empty_choice'] = TRUE;

        return $options;
    }

    protected function listRequiredSchemaKeys()
    {
        return \array_merge(parent::listRequiredSchemaKeys(), ['choices']);
    }

    /**
     * @return string[]
     */
    protected function listValidValues()
    {
        $values = [];
        foreach ($this->choice_list as $choice) {
            $values[] = $choice['value'];
        }

        return $values;
    }

}
