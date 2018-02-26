<?php
/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */

namespace Ingenerator\Form;


use Ingenerator\Form\Element\BodyTextFormElement;
use Ingenerator\Form\Element\Field\AbstractFormField;
use Ingenerator\Form\Element\Field\ChoiceField;
use Ingenerator\Form\Element\Field\DateField;
use Ingenerator\Form\Element\Field\GroupedChoiceField;
use Ingenerator\Form\Element\Field\TextareaField;
use Ingenerator\Form\Element\Field\TextField;
use Ingenerator\Form\Element\FormGroupElement;
use Ingenerator\Form\Util\FormDataArray;
use Ingenerator\KohanaExtras\Validation\ImmutableKohanaValidation;
use Ingenerator\PHPUtils\Validation\StrictDate;
use Ingenerator\PHPUtils\Validation\ValidNumber;

class FormValidator
{
    /**
     * @var \Ingenerator\KohanaExtras\Validation\ImmutableKohanaValidation
     */
    protected $validator;

    /**
     * @param \Ingenerator\Form\Form $form
     *
     * @return bool
     */
    public function validate(Form $form)
    {
        $this->buildValidatorForForm($form);

        $this->validator->check();

        // Validation errors are in a flat structure by fieldname, convert them to a nested
        // array before they can be assigned back to the form
        $this->nestAndAssignErrorsToForm($form, $this->validator->errors('validation'));

        return $this->validator->isValid();
    }

    /**
     * @param \Ingenerator\Form\Form $form
     */
    protected function buildValidatorForForm(Form $form)
    {
        // Kohana validation only supports a flat array of field => value so the nested array
        // of form data needs to be recursively flattened down before we can validate it
        $data            = $this->flattenFormValuesArray($form->getValues());
        $this->validator = new ImmutableKohanaValidation($data);

        foreach ($form->elements as $element) {
            $this->addRulesForElement($element);
        }
    }

    /**
     * Recursively convert an array of POST-style nested data to a flat structure with fieldnames
     *
     * e.g.
     *   ['person' => ['address' => ['street' => 'thing']]]
     *
     * becomes
     *   ['person[address][street]' => 'thing'
     *
     * This is so it can be validated with the standard non-recursive kohana validator
     *
     * @param array $fields
     * @param null  $path_prefix
     * @param array $flat_values
     *
     * @return array
     */
    protected function flattenFormValuesArray(
        array $fields,
        $path_prefix = NULL,
        & $flat_values = []
    ) {
        foreach ($fields as $index => $value) {
            $field_path = ($path_prefix ? $path_prefix.'['.$index.']' : $index);
            if (is_array($value)) {
                $this->flattenFormValuesArray($value, $field_path, $flat_values);
            } else {
                $flat_values[$field_path] = $value;
            }
        }

        return $flat_values;
    }

    /**
     * @param $element
     */
    protected function addRulesForElement($element)
    {
        if ($element instanceof BodyTextFormElement) {
            // Nothing to validate
        } elseif ($element instanceof TextField) {
            $this->addTextFieldRules($element);
        } elseif ($element instanceof TextareaField) {
            $this->addTextareaFieldRules($element);
        } elseif ($element instanceof DateField) {
            $this->addDateFieldRules($element);
        } elseif ($element instanceof GroupedChoiceField) {
            $this->addGroupedChoiceFieldRules($element);
        } elseif ($element instanceof ChoiceField) {
            $this->addChoiceFieldRules($element);
        } elseif ($element instanceof FormGroupElement) {
            $this->addRulesForGroupFields($element);
        } else {
            throw UnsupportedValidationException::badClass($element);
        }
    }

    protected function addTextFieldRules(TextField $field)
    {
        if ($field->text_type == 'text') {
            $this->addTextTextRules($field);
        } elseif ($field->text_type == 'email') {
            $this->addTextEmailRules($field);
        } elseif ($field->text_type == 'number') {
            $this->addTextNumberRules($field);
        } else {
            throw UnsupportedValidationException::badTextType($field);
        }
    }

    protected function addTextTextRules(TextField $field)
    {
        $constraints = $this->listSupportedConstraints($field, ['required']);
        if (isset($constraints['required'])) {
            $this->addRule($field->name, $field->label, 'not_empty');
        }
    }

    protected function listSupportedConstraints(AbstractFormField $field, $allowed_constraints = [])
    {
        $constraints = [];
        foreach ($field->constraints as $key => $value) {
            if (is_numeric($key)) {
                $key = $value;
            }

            if ( ! in_array($key, $allowed_constraints)) {
                throw UnsupportedValidationException::badConstraint($field, $key);
            }
            $constraints[$key] = $value;
        }

        return $constraints;
    }

    /**
     * @param string     $fieldname
     * @param string     $label
     * @param string     $rule
     * @param array|NULL $options
     */
    protected function addRule($fieldname, $label, $rule, array $options = NULL)
    {
        $this->validator->label($fieldname, $label);
        $this->validator->rule($fieldname, $rule, $options);
    }

    protected function addTextEmailRules(TextField $field)
    {
        $this->addRule($field->name, $field->label, 'email');

        $constraints = $this->listSupportedConstraints($field, ['required']);
        if (isset($constraints['required'])) {
            $this->addRule($field->name, $field->label, 'not_empty');
        }
    }

    protected function addTextNumberRules(TextField $field)
    {
        $constraints = $this->listSupportedConstraints($field, ['required', 'step', 'min']);

        if (isset($constraints['step']) AND (int) $constraints['step'] !== 1) {
            throw UnsupportedValidationException::badConstraint(
                $field,
                'step='.$constraints['step']
            );
        }

        $this->addRule($field->name, $field->label, 'digit');

        if (isset($constraints['min'])) {
            $this->addRule(
                $field->name,
                $field->label,
                ValidNumber::rule('minimum'),
                [':value', $constraints['min']]
            );
        }

        if (isset($constraints['required'])) {
            $this->addRule($field->name, $field->label, 'not_empty');
        }
    }

    protected function addTextareaFieldRules(TextareaField $field)
    {
        foreach ($field->constraints as $constraint) {
            if ($constraint === 'required') {
                $this->addRule($field->name, $field->label, 'not_empty');
            } else {
                throw UnsupportedValidationException::badConstraint($field, $constraint);
            }
        }
    }

    protected function addDateFieldRules(DateField $field)
    {
        $constraints = $this->listSupportedConstraints($field, ['required']);
        $this->addRule($field->name, $field->label, StrictDate::rule('date_immutable'));
        if (isset($constraints['required'])) {
            $this->addRule($field->name, $field->label, 'not_empty');
        }
    }

    protected function addGroupedChoiceFieldRules(GroupedChoiceField $field)
    {
        $constraints = $this->listSupportedConstraints($field, ['required']);
        $this->addRule($field->name, $field->label, 'in_array', [':value', $field->valid_values]);

        if (isset($constraints['required'])) {
            $this->addRule($field->name, $field->label, 'not_empty');
        }
    }

    protected function addChoiceFieldRules(ChoiceField $field)
    {
        $constraints = $this->listSupportedConstraints($field, ['required']);
        $this->addRule($field->name, $field->label, 'in_array', [':value', $field->valid_values]);

        if (isset($constraints['required'])) {
            $this->addRule($field->name, $field->label, 'not_empty');
        }
    }

    protected function addRulesForGroupFields(FormGroupElement $group)
    {
        foreach ($group->fields as $element) {
            $this->addRulesForElement($element);
        }
    }

    /**
     * Convert a flat array of field errors produced by Kohana validator into a nested structure
     * and assign them to the form.
     *
     * e.g.
     *   ['person[0][email]' => ['This must be a valid email']]
     *
     * becomes
     *   ['person' => [0 => ['email' => ['This must be a valid email']]]]
     *
     * @param \Ingenerator\Form\Form $form
     * @param array                  $errors
     */
    protected function nestAndAssignErrorsToForm(Form $form, array $errors)
    {
        // FormDataArray already has support for this fieldname => nested value conversion
        $converter = new FormDataArray([]);
        foreach ($errors as $flat_name => $error) {
            $converter->setFieldValue($flat_name, $error);
        }

        $form->setErrors($converter->getValues());
    }

}
