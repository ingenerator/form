<?php
/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */

namespace test\unit\Ingenerator\Form\Element\Field;


use Ingenerator\Form\Element\Field\AbstractFormField;
use Ingenerator\Form\Util\FormDataArray;
use test\unit\Ingenerator\Form\Element\BaseFormElementTest;

abstract class BaseFieldTest extends BaseFormElementTest
{

    public function provider_required_options()
    {
        return [
            ['name'],
            ['label']
        ];
    }

    public function test_it_has_unique_id_by_default()
    {
        $field = $this->newSubject();
        $this->assertInternalType('string', $field->id);
        $this->assertSame($field->id, $field->id);
        $this->assertNotEquals($field->id, $this->newSubject()->id);
    }

    public function test_it_accepts_custom_id()
    {
        $this->assertSame('my-custom-field', $this->newSubject(['id' => 'my-custom-field'])->id);
    }

    public function provider_valid_options_and_defaults()
    {
        return [
            ['container_data', [], ['data-showgroup', 'data-show' => 'stuff']],
            ['empty_value', NULL, 'your name here'],
            ['help_text', NULL, 'some <em>help</em> text'],
            ['highlight_if', [], ['empty']],
            ['hide_display_if', [], ['value:anything']],
        ];
    }

    public function test_it_has_empty_constraints_by_default()
    {
        $this->assertSame([], $this->newSubject()->constraints);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_it_throws_if_constraints_not_specified_as_array()
    {
        $this->newSubject(['constraints' => 'required']);
    }

    /**
     * @testWith [{"label": "Name"}, "Name"]
     *           [{"label": "Name", "display_label": "Candidate name"}, "Candidate name"]
     */
    public function test_it_supports_display_label_defaulting_to_match_label(
        array $options,
        $expect
    ) {
        $subject = $this->newSubject($options);
        $this->assertSame($expect, $subject->display_label);
    }

    /**
     * @testWith ["value"]
     *           ["errors"]
     *
     * @expectedException \InvalidArgumentException
     */
    public function test_it_throws_on_attempt_to_init_value_or_error_as_schema_property($field)
    {
        $this->newSubject([$field => 'anything']);
    }

    public function test_its_errors_are_empty_initially()
    {
        $this->assertSame([], $this->newSubject()->errors);
    }

    /**
     * @testWith [{}, []]
     *           [{"field": ["first error"]}, ["first error"]]
     *           [{"field": ["first error", "other error"]}, ["first error", "other error"]]
     *           [{"field": "just the one error"}, ["just the one error"]]
     */
    public function test_it_can_assign_single_or_collection_of_errors_for_fieldname(array $errors, $expect)
    {
        $subject = $this->newSubject(['name' => 'field']);
        $subject->assignErrors(new FormDataArray($errors));
        $this->assertSame($expect, $subject->errors);
    }

    /**
     * @testWith [{}, null]
     *           [{"empty_value": "You didn't do this"}, "You didn't do this"]
     */
    public function test_its_display_value_defaults_to_empty_value_if_empty($schema, $expect)
    {
        $subject = $this->newSubject($schema);
        $this->assertSame($expect, $subject->display_value);
    }

    /**
     * @param array $values
     *
     * @return AbstractFormField
     */
    protected function newSubject(array $values = [])
    {
        throw new \BadMethodCallException('Base abstract '.__METHOD__.' called');
    }

}
