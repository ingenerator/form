<?php
/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */

namespace test\unit\Ingenerator\Form\Element\Field;


use Ingenerator\Form\Element\Field\GroupedChoiceField;
use Ingenerator\Form\Util\FormDataArray;
use InvalidArgumentException;

class GroupedChoiceFieldTest extends BaseFieldTest
{
    public function test_it_is_initialisable_from_schema_array()
    {
        $this->assertInstanceOf(
            GroupedChoiceField::class,
            $this->newSubject()
        );
    }

    public function provider_required_options()
    {
        $required   = parent::provider_required_options();
        $required[] = ['choice_groups'];

        return $required;
    }


    public function provider_valid_options_and_defaults()
    {
        $options   = parent::provider_valid_options_and_defaults();
        $options['length'] = ['length', NULL, 'short'];
        $options['add_empty_choice'] = ['add_empty_choice', TRUE, FALSE];

        return $options;
    }

    public function provider_invalid_choice_groups()
    {
        return [
            [
                [['junk' => 'stuff']]
            ],
            [
                [['group_caption' => 'no choices']],
            ],
            [
                [['choices' => [['value' => '1', 'caption' => 'no group caption']]]],
            ],
            [
                [
                    [
                        'group_caption' => 'nonsense choices',
                        'choices'       => [['wrong' => 'thing']]
                    ]
                ]
            ],
            [
                [
                    [
                        'group_caption' => 'string choices',
                        'choices'       => ['wrong field']
                    ]
                ]
            ],
            [
                [
                    [
                        'group_caption' => 'bad choices',
                        'choices'       => [['caption' => 'no value']]
                    ]
                ]
            ],
            [
                [
                    [
                        'group_caption' => 'bad choices',
                        'choices'       => [['value' => 'no caption']]
                    ]
                ]
            ],
        ];
    }

    /**
     * @dataProvider provider_invalid_choice_groups
     */
    public function test_it_throws_if_choice_groups_not_valid($choice_groups)
    {
        $this->expectException(InvalidArgumentException::class);
        $this->newSubject(['choice_groups' => $choice_groups]);
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

    public function test_only_empty_value_is_selected_by_default()
    {
        $subject = $this->newSubject(
            [
                'choice_groups' => [
                    [
                        'group_caption' => 'First',
                        'choices'       => [['value' => 'One', 'caption' => 'One']]
                    ],
                    [
                        'group_caption' => 'Second',
                        'choices'       => [
                            ['value' => 'Two', 'caption' => 'Two'],
                            ['value' => 3, 'caption' => 'Three'],
                        ]
                    ]
                ]
            ]
        );
        $this->assertChoiceSelected('_empty_', $subject);
    }

    public function provider_value_assignment()
    {
        return [
            ['', ['html' => '', 'selected' => '_empty_', 'display' => NULL]],
            [NULL, ['html' => '', 'selected' => '_empty_', 'display' => NULL]],
            ['Junk', ['html' => 'Junk', 'selected' => '_empty_', 'display' => NULL]],
            ['1', ['html' => '1', 'selected' => '1', 'display' => 'One']],
            ['Z', ['html' => 'Z', 'selected' => 'Z', 'display' => 'Two']],
            ['3', ['html' => '3', 'selected' => '3', 'display' => 'Three']],
            [3, ['html' => '3', 'selected' => '3', 'display' => 'Three']],
        ];
    }

    /**
     * @dataProvider provider_value_assignment
     */
    public function test_it_can_assign_value_and_mark_option_selected_or_empty_if_invalid(
        $assign,
        $expect
    ) {
        $subject = $this->newSubject(
            [
                'name'          => 'field',
                'choice_groups' => [
                    [
                        'group_caption' => 'First',
                        'choices'       => [['value' => '1', 'caption' => 'One']]
                    ],
                    [
                        'group_caption' => 'Second',
                        'choices'       => [
                            ['value' => 'Z', 'caption' => 'Two'],
                            ['value' => 3, 'caption' => 'Three'],
                        ]
                    ]
                ]
            ]
        );
        $subject->assignValue(new FormDataArray(['field' => $assign]));
        $this->assertSame($expect['html'], $subject->html_value, 'Should have html value');
        $this->assertSame($expect['display'], $subject->display_value, 'Should have display value');
        $this->assertChoiceSelected($expect['selected'], $subject);
    }

    public function test_it_can_list_all_valid_choice_values()
    {
        $subject = $this->newSubject(
            [
                'name'          => 'field',
                'choice_groups' => [
                    [
                        'group_caption' => 'First',
                        'choices'       => [['value' => '1', 'caption' => 'One']]
                    ],
                    [
                        'group_caption' => 'Second',
                        'choices'       => [
                            ['value' => 'Z', 'caption' => 'Two'],
                            ['value' => 3, 'caption' => 'Three'],
                        ]
                    ]
                ]
            ]
        );
        $this->assertSame(['1', 'Z', '3'], $subject->valid_values);
    }

    public function test_it_supports_optional_arbitrary_data_attributes_on_options()
    {
        $subject = $this->newSubject(
            [
                'name'          => 'field',
                'choice_groups' => [
                    [
                        'group_caption' => 'First',
                        'choices'       => [
                            ['value' => '1', 'caption' => 'One', 'data' => ['data-start' => 'foo']]
                        ]
                    ],
                    [
                        'group_caption' => 'Second',
                        'choices'       => [
                            ['value' => 'Z', 'caption' => 'Two'],
                            ['value' => 3, 'caption' => 'Three', 'data' => ['data-anything']],
                        ]
                    ]
                ]
            ]
        );
        $actual  = [];
        foreach ($subject->choice_groups as $group) {
            foreach ($group['choices'] as $choice) {
                $actual[$choice['value']] = $choice['data'];
            }
        }
        $this->assertSame(
            [
                '1' => ['data-start' => 'foo'],
                'Z' => [],
                '3' => ['data-anything']
            ],
            $actual
        );
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
     * @param array $values
     *
     * @return GroupedChoiceField
     */
    protected function newSubject(array $values = [])
    {
        $default = [
            'name'          => 'foofield',
            'label'         => 'What\'s the best foo?',
            'choice_groups' => [
                [
                    'group_caption' => 'Foo',
                    'choices'       => [['value' => 9, 'caption' => 'Nine']]
                ]
            ]
        ];

        return new GroupedChoiceField(\array_merge($default, $values));
    }

    /**
     * @param string             $expect
     * @param GroupedChoiceField $field
     */
    protected function assertChoiceSelected($expect, GroupedChoiceField $field)
    {
        $selected = [];
        if ($field->is_empty_selected) {
            $selected[] = '_empty_';
        }
        foreach ($field->choice_groups as $group) {
            foreach ($group['choices'] as $choice) {
                if ($choice['selected'] === 'selected') {
                    $selected[] = $choice['value'];
                } else {
                    $this->assertSame('', $choice['selected'], 'Should be empty');
                }
            }
        }
        $this->assertEquals([$expect], $selected, 'Expect correct selected choice(s)');
    }


}
