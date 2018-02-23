<?php
/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */

namespace test\unit\Ingenerator\Form\Element\Field;


use Ingenerator\Form\Util\FormDataArray;

class ChoiceFieldTest extends BaseFieldTest
{
    public function test_it_is_initialisable_from_schema_array()
    {
        $this->assertInstanceOf(
            \Ingenerator\Form\Element\Field\ChoiceField::class,
            $this->newSubject()
        );
    }

    public function provider_required_options()
    {
        $required   = parent::provider_required_options();
        $required[] = ['choices'];

        return $required;
    }


    public function provider_valid_options_and_defaults()
    {
        $options   = parent::provider_valid_options_and_defaults();
        $options[] = ['length', NULL, 'short'];
        $options[] = ['add_empty_choice', TRUE, FALSE];

        return $options;
    }

    public function provider_invalid_choices()
    {
        return [
            [[new \stdClass]],
            [[['junk' => 'wrong']]],
            [[['caption' => 'missing value']]],
            [[['value' => 'missing caption']]],
            [[['value' => '1', 'caption' => 'One'], ['second' => 'junk']]]
        ];
    }

    /**
     * @dataProvider provider_invalid_choices
     * @expectedException \InvalidArgumentException
     */
    public function test_it_throws_if_choices_not_simple_string_or_value_caption_array($choices)
    {
        $this->newSubject(['choices' => $choices]);
    }

    /**
     * @testWith [{"constraints": ["required"]}]
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

    public function provider_auto_empty_choice()
    {
        $auto_empty = [
            'value'    => '',
            'caption'  => NULL,
            'selected' => 'selected',
            'disabled' => 'disabled'
        ];
        $one_one    = ['value' => '1', 'caption' => 'One'];

        return [
            [
                // By default and with no empty choice in list, prepends auto-empty
                ['choices' => ['One']],
                [
                    $auto_empty,
                    ['value' => 'One', 'caption' => 'One', 'selected' => '', 'disabled' => ''],
                ]
            ],
            [
                // By default and with no empty choice in list, prepends auto-empty with custom text
                ['choices' => ['One'], 'empty_value' => 'Go on, select'],
                [
                    array_merge($auto_empty, ['caption' => 'Go on, select']),
                    ['value' => 'One', 'caption' => 'One', 'selected' => '', 'disabled' => ''],
                ]
            ],
            [
                // Explicit empty choice in list is selectable and prevents prepending auto
                ['choices' => ['One', '']],
                [
                    ['value' => 'One', 'caption' => 'One', 'selected' => '', 'disabled' => ''],
                    ['value' => '', 'caption' => '', 'selected' => 'selected', 'disabled' => ''],
                ]
            ],
            [
                // Auto-empty choice can be disabled
                ['add_empty_choice' => FALSE, 'choices' => ['One']],
                [
                    ['value' => 'One', 'caption' => 'One', 'selected' => '', 'disabled' => ''],
                ]
            ],
            [
                // By default and with no empty choice in list, prepends auto-empty
                ['choices' => [$one_one]],
                [
                    $auto_empty,
                    ['value' => '1', 'caption' => 'One', 'selected' => '', 'disabled' => ''],
                ]
            ],
            [
                // By default and with no empty choice in list, prepends auto-empty with custom text
                ['choices' => [$one_one], 'empty_value' => 'Do it'],
                [
                    array_merge($auto_empty, ['caption' => 'Do it']),
                    ['value' => '1', 'caption' => 'One', 'selected' => '', 'disabled' => ''],
                ]
            ],
            [
                // Explicit empty choice in list is selectable and prevents prepending auto
                ['choices' => [$one_one, ['value' => '', 'caption' => '']]],
                [
                    ['value' => '1', 'caption' => 'One', 'selected' => '', 'disabled' => ''],
                    ['value' => '', 'caption' => '', 'selected' => 'selected', 'disabled' => ''],
                ]
            ],
            [
                // Auto-empty choice can be disabled
                ['add_empty_choice' => FALSE, 'choices' => [$one_one]],
                [
                    ['value' => '1', 'caption' => 'One', 'selected' => '', 'disabled' => ''],
                ]
            ],

        ];
    }

    /**
     * @dataProvider provider_auto_empty_choice
     */
    public function test_it_prepends_empty_choice_unless_disabled_or_explicit_empty_choice_present(
        $schema,
        $expect_choices
    ) {
        $subject = $this->newSubject($schema);
        $this->assertSame($expect_choices, $subject->choices);
    }

    public function test_it_provides_array_of_choices_from_scalar_choices_list()
    {
        $subject = $this->newSubject(
            ['add_empty_choice' => FALSE, 'choices' => ['First', 'Second', 3]]
        );
        $this->assertSame(
            [
                ['value' => 'First', 'caption' => 'First', 'selected' => '', 'disabled' => ''],
                ['value' => 'Second', 'caption' => 'Second', 'selected' => '', 'disabled' => ''],
                ['value' => '3', 'caption' => '3', 'selected' => '', 'disabled' => '']
            ],
            $subject->choices
        );
    }

    public function test_it_provides_array_of_choices_from_value_caption_choices_list()
    {
        $subject = $this->newSubject(
            [
                'add_empty_choice' => FALSE,
                'choices'          => [
                    ['value' => 1, 'caption' => 'Any'],
                    ['value' => 9, 'caption' => 'Thing']
                ]
            ]
        );
        $this->assertSame(
            [
                ['value' => '1', 'caption' => 'Any', 'selected' => '', 'disabled' => ''],
                ['value' => '9', 'caption' => 'Thing', 'selected' => '', 'disabled' => '']
            ],
            $subject->choices
        );
    }

    public function provider_value_assignment()
    {
        $choice_strings = ['First', 'Second'];
        $choice_list    = [
            ['value' => 1, 'caption' => 'One'],
            ['value' => '2', 'caption' => 'Two']
        ];

        return [
            [$choice_strings, '', ['html' => '', 'selected' => '', 'display' => NULL]],
            [$choice_strings, NULL, ['html' => '', 'selected' => '', 'display' => NULL]],
            [$choice_strings, 'Junk', ['html' => 'Junk', 'selected' => '', 'display' => NULL]],
            [$choice_strings, 'Second', ['html' => 'Second', 'selected' => 'Second', 'display' => 'Second']],
            [$choice_list, '', ['html' => '', 'selected' => '', 'display' => NULL]],
            [$choice_list, NULL, ['html' => '', 'selected' => '', 'display' => NULL]],
            [$choice_list, 'Junk', ['html' => 'Junk', 'selected' => '', 'display' => NULL]],
            [$choice_list, 'One', ['html' => 'One', 'selected' => '', 'display' => NULL]],
            [$choice_list, 1, ['html' => '1', 'selected' => '1', 'display' => 'One']],
            [$choice_list, 2, ['html' => '2', 'selected' => '2', 'display' => 'Two']],
        ];
    }

    /**
     * @dataProvider provider_value_assignment
     */
    public function test_it_can_assign_value_and_mark_appropriate_choice_selected_or_empty_if_invalid(
        $choices,
        $assign_val,
        $expect
    ) {
        $subject = $this->newSubject(['name' => 'field', 'choices' => $choices]);
        $subject->assignValue(new FormDataArray(['field' => $assign_val]));
        $this->assertSame($expect['html'], $subject->html_value, 'Should match HTML');
        $this->assertSame($expect['display'], $subject->display_value, 'Should match display');
        $this->assertChoiceSelected($expect['selected'], $subject->choices);
    }

    /**
     * @testWith ["", null]
     *           ["anything", "anything"]
     */
    public function test_it_maps_html_value_to_null_or_string_for_collection($html_value, $expect)
    {
        $subject = $this->newSubject(['name' => 'field']);
        $subject->assignValue(new FormDataArray(['field' => $html_value]));

        $this->assertCollectsValues(['field' => $expect], $subject);
    }

    /**
     * @testWith [["One", "Two"], ["One", "Two"]]
     *           [[{"value": 1, "caption": "One"}, {"value": 9, "caption": "One"}], ["1", "9"]]
     */
    public function test_it_can_list_all_valid_choice_values($choices, $expect)
    {
        $subject = $this->newSubject(['choices' => $choices]);
        $this->assertSame($expect, $subject->valid_values);
    }

    /**
     * @param array $values
     *
     * @return \Ingenerator\Form\Element\Field\ChoiceField
     */
    protected function newSubject(array $values = [])
    {
        $default = [
            'name'    => 'foofield',
            'label'   => 'What\'s the best foo?',
            'choices' => ['One']
        ];

        return new \Ingenerator\Form\Element\Field\ChoiceField(array_merge($default, $values));
    }

    /**
     * @param string $expect
     * @param array  $choices
     */
    protected function assertChoiceSelected($expect, array $choices)
    {
        $selected = [];
        foreach ($choices as $choice) {
            if ($choice['selected'] === 'selected') {
                $selected[] = $choice['value'];
            } else {
                $this->assertEquals('', $choice['selected']);
            }
        }
        $this->assertEquals([$expect], $selected, 'Expect correct selected choices');
    }


}
