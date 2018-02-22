<?php
/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */

namespace test\unit\Ingenerator\Form\Element\Field;


use Ingenerator\Form\Element\Field\RepeatingGroupField;
use Ingenerator\Form\Element\Field\TextField;
use Ingenerator\Form\Util\FormDataArray;

class RepeatingGroupFieldTest extends BaseFieldTest
{
    public function test_it_is_initialisable()
    {
        $this->assertInstanceOf(RepeatingGroupField::class, $this->newSubject());
    }

    public function provider_required_options()
    {
        $required   = parent::provider_required_options();
        $required[] = ['fields'];

        return $required;
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_it_throws_if_any_html5_constraints_specified()
    {
        $this->newSubject(['constraints' => ['required']]);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_it_throws_if_contained_fieldname_not_wrapped_in_brackets()
    {
        $this->newSubject(['name' => 'jobs', 'fields' => [['type' => 'text', 'name' => 'wrong']]]);
    }

    public function test_it_has_group_zero_with_all_defined_subfields_by_default()
    {
        $subject = $this->newSubject(
            [
                'name'   => 'jobs',
                'fields' => [
                    ['type' => 'text', 'name' => '[title]', 'label' => 'Title'],
                    ['type' => 'text', 'name' => '[boss]', 'label' => 'Boss'],
                ]
            ]
        );

        $this->assertGroupFieldsEqual(
            [
                [
                    ['class' => TextField::class, 'name' => 'jobs[0][title]', 'value' => ''],
                    ['class' => TextField::class, 'name' => 'jobs[0][boss]', 'value' => ''],
                ]
            ],
            $subject->groups
        );
    }

    public function test_it_dynamically_creates_groups_and_assigns_values_when_assigning()
    {
        $subject = $this->newSubject(
            [
                'name'   => 'jobs',
                'fields' => [
                    ['type' => 'text', 'name' => '[title]', 'label' => 'Title'],
                    ['type' => 'text', 'name' => '[boss]', 'label' => 'Boss'],
                ]
            ]
        );
        $subject->assignValue(
            new FormDataArray(
                [
                    'jobs' => [
                        0 => ['title' => 'Gold', 'boss' => 'Skywalk'],
                        9 => ['title' => 'Kylo', 'boss' => 'Vader'],
                    ]
                ]
            )
        );

        $this->assertGroupFieldsEqual(
            [
                [
                    ['class' => TextField::class, 'name' => 'jobs[0][title]', 'value' => 'Gold'],
                    ['class' => TextField::class, 'name' => 'jobs[0][boss]', 'value' => 'Skywalk'],
                ],
                [
                    ['class' => TextField::class, 'name' => 'jobs[9][title]', 'value' => 'Kylo'],
                    ['class' => TextField::class, 'name' => 'jobs[9][boss]', 'value' => 'Vader'],
                ],
            ],
            $subject->groups
        );
    }

    public function test_it_removes_previously_defined_groups_when_not_present_in_assigned_values()
    {
        $subject = $this->newSubject(
            [
                'name'   => 'jobs',
                'fields' => [
                    ['type' => 'text', 'name' => '[title]', 'label' => 'Title'],
                    ['type' => 'text', 'name' => '[boss]', 'label' => 'Boss'],
                ]
            ]
        );
        $subject->assignValue(
            new FormDataArray(
                [
                    'jobs' => [
                        0 => ['title' => 'Gold', 'boss' => 'Skywalk'],
                        9 => ['title' => 'Kylo', 'boss' => 'Vader'],
                    ]
                ]
            )
        );
        $subject->assignValue(
            new FormDataArray(['jobs' => [9 => ['title' => 'Kylo', 'boss' => 'Vader']]])
        );

        $this->assertGroupFieldsEqual(
            [
                [
                    ['class' => TextField::class, 'name' => 'jobs[9][title]', 'value' => 'Kylo'],
                    ['class' => TextField::class, 'name' => 'jobs[9][boss]', 'value' => 'Vader'],
                ],
            ],
            $subject->groups
        );
    }

    public function test_it_builds_single_empty_group_when_no_empty_data_assigned()
    {
        $subject = $this->newSubject(
            [
                'name'   => 'jobs',
                'fields' => [
                    ['type' => 'text', 'name' => '[title]', 'label' => 'Title'],
                    ['type' => 'text', 'name' => '[boss]', 'label' => 'Boss'],
                ]
            ]
        );
        $subject->assignValue(new FormDataArray([]));

        $this->assertGroupFieldsEqual(
            [
                [
                    ['class' => \Ingenerator\Form\Element\Field\TextField::class, 'name' => 'jobs[0][title]', 'value' => ''],
                    ['class' => TextField::class, 'name' => 'jobs[0][boss]', 'value' => ''],
                ],
            ],
            $subject->groups
        );

    }

    /**
     * @testWith [{}, [{"title": null, "boss": null}]]
     *           [{"1": {"title": "Leader", "boss": "CEO"}}, {"1":{"title": "Leader", "boss": "CEO"}}]
     *           [{"1": {"title": "Leader", "boss": "CEO"}, "9":{"title": "CEO", "boss": "Board"}}, {"1": {"title": "Leader", "boss": "CEO"}, "9":{"title": "CEO", "boss": "Board"}}]
     */
    public function test_it_collects_values_from_all_group_fields($assign, $expect)
    {
        $subject = $this->newSubject(
            [
                'name'   => 'jobs',
                'fields' => [
                    ['type' => 'text', 'name' => '[title]', 'label' => 'Title'],
                    ['type' => 'text', 'name' => '[boss]', 'label' => 'Boss'],
                ]
            ]
        );
        $subject->assignValue(new FormDataArray(['jobs' => $assign]));
        $this->assertCollectsValues(['jobs' => $expect], $subject);
    }

    public function test_it_supports_non_value_elements_within_the_group()
    {
        // This isn't especially good UX - you almost certainly want instead to have a single
        // body-text above/below the repeating group, but just to ensure that we can support
        // elements that don't have a fieldname within the group.
        $subject = $this->newSubject(
            [
                'name'   => 'jobs',
                'fields' => [
                    ['type' => 'body-text', 'content' => 'Explain yourself'],
                    ['type' => 'text', 'name' => '[explain]', 'label' => 'Explanation'],
                ]
            ]
        );
        $subject->assignValue(new FormDataArray(['jobs' => [9 => ['explain' => 'OK']]]));
        $this->assertCollectsValues(['jobs' => [9 => ['explain' => 'OK']]], $subject);
    }

    public function test_it_assigns_errors_to_child_fields_in_each_group()
    {
        $this->markTestIncomplete();
    }

    /**
     * @param array $values
     *
     * @return \Ingenerator\Form\Element\Field\RepeatingGroupField
     */
    protected function newSubject(array $values = [])
    {
        $default = [
            'name'   => 'education',
            'label'  => 'Education',
            'fields' => [
                ['type' => 'text', 'name' => '[foo]', 'label' => 'Foo']
            ]
        ];

        return new \Ingenerator\Form\Element\Field\RepeatingGroupField(array_merge($default, $values), $this->getElementFactory());
    }

    /**
     * @param $expect
     * @param $groups
     */
    protected function assertGroupFieldsEqual(array $expect, array $groups)
    {
        $actual = [];
        foreach ($groups as $index => $group) {
            foreach ($group as $field) {
                /** @var \Ingenerator\Form\Element\Field\AbstractFormField $field */
                $actual[$index][] = [
                    'class' => get_class($field),
                    'name'  => $field->name,
                    'value' => $field->html_value
                ];
            }
        }
        $this->assertEquals($expect, $actual);
    }


}


