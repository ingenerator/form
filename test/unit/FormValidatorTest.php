<?php
/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */

namespace test\unit\Ingenerator\Form;


use Ingenerator\Form\Form;
use Ingenerator\Form\FormConfig;
use Ingenerator\Form\FormElementFactory;
use Ingenerator\Form\FormValidator;
use Ingenerator\KohanaExtras\Validation\TestConstraint\ValidationRulesMatch;
use Ingenerator\PHPUtils\Object\ObjectPropertyRipper;
use test\unit\BaseTestCase;

class FormValidatorTest extends \PHPUnit\Framework\TestCase
{

    public function test_it_is_initialisable()
    {
        $this->assertInstanceOf(FormValidator::class, $this->newSubject());
    }

    public function provider_supported_field_types()
    {
        return [
            [['type' => 'text']],
            [['type' => 'text', 'text_type' => 'email']],
            [['type' => 'textarea']],
            [['type' => 'date']],
            [['type' => 'grouped-choice']],
            [['type' => 'group']],
            [['type' => 'choice']],
            [['type' => 'choice-radio']],
        ];
    }

    public function provider_unsupported_field_types()
    {
        // This is a safety test. If you try to use the server-side validator on a form that has
        // constraints we have not implemented yet, it will throw at you (at development time).
        // This is to ensure you don't unknowingly open an unvalidated field to the world.
        // In order to implement support for a new constraint, add a simple example of it to the
        // provider_supported_field_types. Any defined field types that are not in there will be
        // added to the automatic `unsupported` list.
        $all_types = FormConfig::withDefaults()->listDefinedElementTypes();
        $supported = ['body-text'];
        foreach ($this->provider_supported_field_types() as $supported_type) {
            $supported[] = $supported_type[0]['type'];
        }
        $unsupported = [];
        foreach (array_diff($all_types, array_unique($supported)) as $type) {
            $unsupported[] = [['type' => $type]];
        }

        $unsupported[] = [
            [
                'type'        => 'text',
                'text_type'   => 'number',
                'constraints' => ['step' => 0.1],
            ],
        ];
        $unsupported[] = [
            [
                'type'        => 'text',
                'text_type'   => 'number',
                'constraints' => ['max' => 15],
            ],
        ];
        $unsupported[] = [['type' => 'text', 'text_type' => uniqid('anything')]];
        $unsupported[] = [['type' => 'text', 'constraints' => ['pattern']]];
        $unsupported[] = [['type' => 'text', 'constraints' => ['required', 'pattern']]];

        return $unsupported;
    }

    /**
     * @dataProvider provider_unsupported_field_types
     * @expectedException \Ingenerator\Form\UnsupportedValidationException
     */
    public function test_it_throws_if_unsupported_field_types_or_constraints($element_schema)
    {
        $form = $this->givenFormWithElements($element_schema);
        $this->newSubject()->validate($form);
    }

    /**
     * @dataProvider  provider_supported_field_types
     */
    public function test_it_validates_supported_types_without_throwing($element)
    {
        $form = $this->givenFormWithElements($element);
        $this->assertTrue($this->newSubject()->validate($form));
    }

    public function test_it_builds_validation_with_constraints_for_required_text_field()
    {
        $form    = $this->givenFormWithElements(
            [
                'type'        => 'text',
                'constraints' => ['required'],
                'name'        => 'information',
            ]
        );
        $subject = $this->newSubject();
        $subject->validate($form);
        $this->assertBuildsValidationWithRules(
            ['information' => ['not_empty' => [':value']]],
            $subject
        );
    }

    /**
     * @testWith [[], {"email": [":value"]}]
     *           [["required"], {"email": [":value"], "not_empty": [":value"]}]
     */
    public function test_it_builds_validation_with_constraints_for_email_text_field(
        $constraints,
        $expect
    ) {
        $form    = $this->givenFormWithElements(
            [
                'type'        => 'text',
                'text_type'   => 'email',
                'constraints' => $constraints,
                'name'        => 'user',
            ]
        );
        $subject = $this->newSubject();
        $subject->validate($form);
        $this->assertBuildsValidationWithRules(['user' => $expect], $subject);
    }

    /**
     * @testWith [[], {"digit": [":value"]}]
     *           [["required"], {"digit": [":value"], "not_empty": [":value"]}]
     *           [{"step": 1}, {"digit": [":value"]}]
     *           [{"min": 15, "step": 1}, {"digit": [":value"], "Ingenerator\\PHPUtils\\Validation\\ValidNumber::minimum": [":value", 15]}]
     */
    public function test_it_builds_validation_with_constraints_for_number_text_field(
        $constraints,
        $expect
    ) {
        $form    = $this->givenFormWithElements(
            [
                'type'        => 'text',
                'text_type'   => 'number',
                'constraints' => $constraints,
                'name'        => 'f',
            ]
        );
        $subject = $this->newSubject();
        $subject->validate($form);
        $this->assertBuildsValidationWithRules(['f' => $expect], $subject);
    }

    /**
     * @testWith [[], []]
     *           [["required"], {"not_empty": [":value"]}]
     */
    public function test_it_builds_validation_with_constraints_for_textarea($constraints, $expect)
    {
        $form    = $this->givenFormWithElements(
            [
                'type'        => 'textarea',
                'constraints' => $constraints,
                'name'        => 'message',
            ]
        );
        $subject = $this->newSubject();
        $subject->validate($form);
        if ($expect) {
            $this->assertBuildsValidationWithRules(['message' => $expect], $subject);
        } else {
            $this->assertBuildsValidationWithRules([], $subject);
        }
    }

    /**
     * @testWith [[], {"Ingenerator\\PHPUtils\\Validation\\StrictDate::date_immutable": [":value"]}]
     *           [["required"], {"Ingenerator\\PHPUtils\\Validation\\StrictDate::date_immutable": [":value"], "not_empty": [":value"]}]
     */
    public function test_it_builds_validation_with_constraints_for_date($constraints, $expect)
    {
        $form    = $this->givenFormWithElements(
            [
                'type'        => 'date',
                'constraints' => $constraints,
                'name'        => 'some_date'
            ]
        );
        $subject = $this->newSubject();
        $subject->validate($form);
        $this->assertBuildsValidationWithRules(['some_date' => $expect], $subject);
    }

    /**
     * @testWith [[], {"in_array": [":value", [1,9,14]]}]
     *           [["required"], {"in_array": [":value", [1,9,14]], "not_empty": [":value"]}]
     */
    public function test_it_builds_validation_with_constraints_for_grouped_choice(
        $constraints,
        $expect
    ) {
        $form    = $this->givenFormWithElements(
            [
                'type'          => 'grouped-choice',
                'constraints'   => $constraints,
                'choice_groups' => [
                    [
                        'group_caption' => 'One',
                        'choices'       => [
                            ['value' => 1, 'caption' => 'One'],
                            ['value' => 9, 'caption' => 'Second'],
                        ]
                    ],
                    [
                        'group_caption' => 'Two',
                        'choices'       => [
                            ['value' => 14, 'caption' => 'Third'],
                        ]
                    ]
                ],
                'name'          => 'some_choice',
            ]
        );
        $subject = $this->newSubject();
        $subject->validate($form);
        $this->assertBuildsValidationWithRules(['some_choice' => $expect], $subject);
    }

    /**
     * @testWith [[], {"in_array": [":value", [1,9,8]]}]
     *           [["required"], {"in_array": [":value", [1,9,8]], "not_empty": [":value"]}]
     */
    public function test_it_builds_validation_with_constraints_for_simple_choice(
        $constraints,
        $expect
    ) {
        $form    = $this->givenFormWithElements(
            [
                'type'        => 'choice',
                'constraints' => $constraints,
                'choices'     => [
                    ['value' => 1, 'caption' => 'One'],
                    ['value' => 9, 'caption' => 'Nine'],
                    ['value' => 8, 'caption' => 'Eight'],
                ],
                'name'        => 'some_choice',
            ]
        );
        $subject = $this->newSubject();
        $subject->validate($form);
        $this->assertBuildsValidationWithRules(['some_choice' => $expect], $subject);
    }

    /**
     * @testWith [[], {"in_array": [":value", [1,9,8]]}]
     *           [["required"], {"in_array": [":value", [1,9,8]], "not_empty": [":value"]}]
     */
    public function test_it_builds_validation_with_constraints_for_choice_radio(
        $constraints,
        $expect
    ) {
        $form    = $this->givenFormWithElements(
            [
                'type'        => 'choice-radio',
                'constraints' => $constraints,
                'choices'     => [
                    ['value' => 1, 'caption' => 'One'],
                    ['value' => 9, 'caption' => 'Nine'],
                    ['value' => 8, 'caption' => 'Eight'],
                ],
                'name'        => 'some_choice',
            ]
        );
        $subject = $this->newSubject();
        $subject->validate($form);
        $this->assertBuildsValidationWithRules(['some_choice' => $expect], $subject);
    }

    public function test_it_builds_validation_for_group_field_with_all_children()
    {
        $form    = $this->givenFormWithElements(
            [
                'type'   => 'group',
                'fields' => [
                    [
                        'type'        => 'text',
                        'name'        => 'one',
                        'label'       => 'O',
                        'constraints' => ['required']
                    ],
                    ['type' => 'text', 'name' => 'info', 'label' => 'Info', 'constraints' => []],
                    [
                        'type'        => 'text',
                        'name'        => 'name',
                        'label'       => 'Name',
                        'constraints' => ['required']
                    ],
                ]
            ]
        );
        $subject = $this->newSubject();
        $subject->validate($form);
        $this->assertBuildsValidationWithRules(
            [
                'one'  => ['not_empty' => [':value']],
                'name' => ['not_empty' => [':value']],
            ],
            $subject
        );
    }

    public function test_it_can_build_validation_for_form_with_nested_field_structure()
    {
        $form    = $this->givenFormWithElements(
            ['type' => 'text', 'name' => 'referee[0][name]', 'constraints' => ['required']],
            ['type' => 'text', 'name' => 'referee[1][name]', 'constraints' => ['required']],
            ['type' => 'text', 'name' => 'person[info][stuff]', 'constraints' => ['required']]
        );
        $subject = $this->newSubject();
        $subject->validate($form);
        $this->assertBuildsValidationWithRules(
            [
                'referee[0][name]'    => ['not_empty' => [':value']],
                'referee[1][name]'    => ['not_empty' => [':value']],
                'person[info][stuff]' => ['not_empty' => [':value']],
            ],
            $subject
        );

    }


    public function provider_full_validation_test()
    {
        // This does not need to test all the varying combinations of constraints
        // it is enough just to test that it is clearly passing the form data into the validator
        // and passing the messages back into the form
        $flat_schema = [
            [
                'type'        => 'text',
                'label'       => 'Name',
                'name'        => 'username',
                'constraints' => ['required'],
            ],
            [
                'type'        => 'text',
                'label'       => 'Your Email',
                'text_type'   => 'email',
                'name'        => 'useremail',
                'constraints' => ['required'],
            ],
            [
                'label' => 'Your Message',
                'type'  => 'textarea',
                'name'  => 'message',
            ]
        ];

        $nested_schema = [
            [
                'type'        => 'text',
                'label'       => 'Name',
                'name'        => 'person[name][first]',
                'constraints' => ['required'],
            ],
            [
                'type'        => 'text',
                'label'       => 'Ref 1 Name',
                'name'        => 'referee[0][name]',
                'constraints' => ['required'],
            ],
            [
                'type'        => 'text',
                'label'       => 'Ref 2 Name',
                'name'        => 'referee[1][name]',
                'constraints' => ['required'],
            ],
            [
                'type'        => 'text',
                'label'       => 'Ref 2 Job',
                'name'        => 'referee[1][job]',
                'constraints' => ['required'],
            ],
        ];

        return [
            [
                $flat_schema,
                ['username' => '', 'useremail' => ''],
                [
                    'username'  => ['Name must not be empty'],
                    'useremail' => ['Your Email must not be empty'],
                ],
                FALSE,
            ],
            [
                $flat_schema,
                ['username' => 'jack', 'useremail' => 'jack bad'],
                [
                    'useremail' => ['Your Email must be an email address'],
                ],
                FALSE,
            ],
            [
                $flat_schema,
                ['username' => 'jack', 'useremail' => 'jack@mail.net', 'message' => 'anything'],
                [],
                TRUE,
            ],
            [
                $nested_schema,
                [],
                [
                    'person[name][first]' => ['Name must not be empty'],
                    'referee[0][name]'    => ['Ref 1 Name must not be empty'],
                    'referee[1][name]'    => ['Ref 2 Name must not be empty'],
                    'referee[1][job]'     => ['Ref 2 Job must not be empty'],
                ],
                FALSE,
            ],
            [
                $nested_schema,
                ['referee' => [1 => ['name' => 'Any', 'job' => 'Any']]],
                [
                    'person[name][first]' => ['Name must not be empty'],
                    'referee[0][name]'    => ['Ref 1 Name must not be empty'],
                ],
                FALSE,
            ],
            [
                $nested_schema,
                [
                    'person'  => ['name' => ['first' => 'Mine']],
                    'referee' => [['name' => 'r1n'], ['name' => 'r2n', 'job' => 'r2job']]
                ],
                [],
                TRUE,
            ],
        ];
    }

    /**
     * @dataProvider provider_full_validation_test
     */
    public function test_it_validates_with_assigned_form_data_and_adds_errors_to_form(
        $elements,
        $data,
        $expect_errors,
        $expect_valid
    ) {
        $form = $this->givenFormWithElementArray($elements);
        $form->setValues($data);

        $this->assertSame(
            $expect_valid,
            $this->newSubject()->validate($form),
            'Expect correct validation result'
        );

        $this->assertEquals($expect_errors, $this->getFormErrors($form));
        $this->assertSame(
            ! empty($expect_errors),
            $form->has_errors,
            'Expect correct form->has_errors'
        );
    }

    protected function newSubject()
    {
        return new FormValidator;
    }

    /**
     * @param array $element,...
     *
     * @return \Ingenerator\Form\Form
     */
    protected function givenFormWithElements($element)
    {
        return $this->givenFormWithElementArray(func_get_args());
    }

    /**
     * @param array $elements
     *
     * @return \Ingenerator\Form\Form
     */
    protected function givenFormWithElementArray(array $elements)
    {
        $default_props = [
            'body-text'        => [],
            'choice'           => [
                'label'   => 'Choice',
                'name'    => 'choice',
                'choices' => ['One'],
            ],
            'choice-or-other'  => [
                'label'            => 'Choice or other',
                'name'             => 'choice_or_other',
                'choices'          => ['One'],
                'other_for_values' => ['One', 'Two'],
            ],
            'choice-radio'           => [
                'label'   => 'Choice Radio',
                'name'    => 'choice_radio',
                'choices' => ['One'],
            ],
            'date'             => [
                'label' => 'Any date',
                'name'  => 'date',
            ],
            'group'            => [
                'label'  => 'Group',
                'fields' => [['name' => 'anything', 'label' => 'stuff', 'type' => 'text']],
            ],
            'grouped-choice'   => [
                'label'         => 'Grouped choice',
                'name'          => 'groupedchoice',
                'choice_groups' => [
                    [
                        'group_caption' => 'Any',
                        'choices'       => [['value' => '1', 'caption' => '2']]
                    ]
                ]
            ],
            'repeating-group'  => [
                'label'  => 'Repeating group',
                'name'   => 'repeating_group',
                'fields' => [['name' => '[anything]', 'label' => 'stuff', 'type' => 'text']],
            ],
            'rough-date-range' => [
                'label' => 'Rough date range',
                'name'  => 'rough_date_range',
            ],
            'text'             => [
                'label' => 'Text',
                'name'  => 'text',
            ],
            'textarea'         => [
                'label' => 'Textarea',
                'name'  => 'textarea',
            ],
            'upload-pdf'       => [
                'label' => 'Upload PDF',
                'name'  => 'upload_pdf',
            ],
        ];

        $elements = array_map(
            function ($element) use ($default_props) {
                return array_merge(\Arr::get($default_props, $element['type'], []), $element);
            },
            $elements
        );

        return new Form(
            ['elements' => $elements],
            new FormElementFactory(FormConfig::withDefaults())
        );
    }

    protected function assertBuildsValidationWithRules($expect, FormValidator $subject)
    {
        // Working proof of all the style validations in one method. It's OK because this will be
        // going soon when we implement a real validator.
        // Remember to at least delete this comment in July 2020.
        $validator = ObjectPropertyRipper::ripOne($subject, 'validator');
        $this->assertThat($validator, new ValidationRulesMatch($expect));
    }

    /**
     * @param $form
     *
     * @return array
     */
    protected function getFormErrors($form)
    {
        $errors = [];
        foreach ($form->elements as $element) {
            /** @var \Ingenerator\Form\Element\Field\AbstractFormField $element */
            $errors[$element->name] = $element->errors;
        }
        $errors = array_filter($errors);

        return $errors;
    }

}
