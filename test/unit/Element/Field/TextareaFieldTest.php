<?php
/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */

namespace test\unit\Ingenerator\Form\Element\Field;


use Ingenerator\Form\Util\FormDataArray;

class TextareaFieldTest extends BaseFieldTest
{
    public function test_it_is_initialisable_from_schema_array()
    {
        $this->assertInstanceOf(
            \Ingenerator\Form\Element\Field\TextareaField::class,
            $this->newSubject()
        );
    }

    public function test_its_default_html_value_is_empty_string()
    {
        $this->assertSame('', $this->newSubject()->html_value);
    }

    public function provider_valid_options_and_defaults()
    {
        $defaults   = parent::provider_valid_options_and_defaults();
        $defaults[] = ['rows', NULL, 10];

        return $defaults;
    }

    /**
     * @testWith [{"constraints": ["required"]}]
     *           [{"constraints": {"maxlength": "30"}}]
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

    /**
     * @testWith [{}, "", null]
     *           [{"field": ""}, "", null]
     *           [{"field": "0"}, "0", null]
     *           [{"field": "anything"}, "anything", "anything"]
     */
    public function test_it_assigns_values(array $values, $expect_html, $expect_display)
    {
        $subject = $this->newSubject(['name' => 'field']);
        $subject->assignValue(new FormDataArray($values));
        $this->assertSame($expect_html, $subject->html_value, 'Should have html value');
        $this->assertSame($expect_display, $subject->display_value, 'Should have display value');
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
     * @testWith ["any stuff", 2]
     *           ["any\nstuff\non\nmultiple lines", 5]
     */
    public function test_with_no_row_count_it_has_enough_rows_to_fit_its_content(
        $value,
        $expect_rows
    ) {
        $subject = $this->newSubject(['name' => 'field']);
        $subject->assignValue(new FormDataArray(['field' => $value]));
        $this->assertSame($expect_rows, $subject->rows);
    }

    public function test_with_explicit_row_count_it_does_not_resize_to_content()
    {
        $subject = $this->newSubject(['name' => 'field', 'rows' => 5]);
        $subject->assignValue(new FormDataArray(['field' => "\n\n\n\n\n\n\n\n\n\n\n\n"]));
        $this->assertSame(5, $subject->rows);
    }

    /**
     * @param array $values
     *
     * @return \Ingenerator\Form\Element\Field\TextareaField
     */
    protected function newSubject(array $values = [])
    {
        $default = [
            'name'  => 'foofield',
            'label' => 'What\'s the best foo?'
        ];

        return new \Ingenerator\Form\Element\Field\TextareaField(array_merge($default, $values));
    }

}


