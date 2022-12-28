<?php
/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */

namespace test\unit\Ingenerator\Form\Element\Field;


use Ingenerator\Form\Element\Field\TextField;
use Ingenerator\Form\Util\FormDataArray;

class TextFieldTest extends BaseFieldTest
{
    public function test_it_is_initialisable_from_schema_array()
    {
        $this->assertInstanceOf(TextField::class, $this->newSubject());
    }

    public function provider_valid_options_and_defaults()
    {
        $options   = parent::provider_valid_options_and_defaults();
        $options['text_type'] = ['text_type', 'text', 'email'];
        $options['length'] = ['length', NULL, 'short'];

        return $options;
    }

    /**
     * @testWith [{"type": "text", "constraints": ["required"]}]
     *           [{"type": "text", "constraints": {"maxlength": "30"}}]
     *           [{"type": "text", "constraints": {"pattern": "^\\d+"}}]
     *           [{"type": "number", "constraints": {"min": "12"}}]
     *           [{"type": "number", "constraints": {"max": "15"}}]
     *           [{"type": "number", "constraints": {"min": 12, "step": 1, "max": 15}}]
     */
    public function test_it_accepts_html5_constraints($schema)
    {
        $subject = $this->newSubject($schema);
        $this->assertSame($schema['constraints'], $subject->constraints);
    }

    public function test_it_throws_with_invalid_html5_constraints()
    {
        $this->markTestIncomplete();
    }

    public function test_its_default_html_value_is_empty_string()
    {
        $this->assertSame('', $this->newSubject()->html_value);
    }

    /**
     * @testWith [{}, "", null]
     *           [{"field": ""}, "", null]
     *           [{"field": "0"}, "0", null]
     *           [{"field": "anything"}, "anything", "anything"]
     */
    public function test_it_can_assign_value(array $values, $expect_html, $expect_display)
    {
        $subject = $this->newSubject(['name' => 'field']);
        $subject->assignValue(new FormDataArray($values));
        $this->assertSame($expect_html, $subject->html_value, 'Should have html value');
        $this->assertSame($expect_display, $subject->display_value, 'display_value should match html_value');
    }

    /**
     * @testWith ["", null]
     *           ["anything", "anything"]
     *           ["0", "0"]
     */
    public function test_it_maps_html_value_to_null_or_string_for_collection($html_value, $expect)
    {
        $subject = $this->newSubject(['name' => 'field']);
        $subject->assignValue(new FormDataArray(['field' => $html_value]));

        $this->assertCollectsValues(['field' => $expect], $subject);
    }

    /**
     * @param array $values
     *
     * @return \Ingenerator\Form\Element\Field\TextField
     */
    protected function newSubject(array $values = [])
    {
        $default = [
            'name'  => 'foofield',
            'label' => 'What\'s the best foo?'
        ];

        return new TextField(\array_merge($default, $values));
    }

}


