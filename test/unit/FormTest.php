<?php
/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */

namespace test\unit\Ingenerator\Form;


use Ingenerator\Form\Element\Field\TextField;
use Ingenerator\Form\Form;
use Ingenerator\Form\TestSupport\PHPUnit10\BaseFormElementTestCase;
use PHPUnit\Framework\Attributes\TestWith;
use function array_merge;

class FormTest extends BaseFormElementTestCase
{
    public static function provider_required_options(): array
    {
        return [
            ['elements']
        ];
    }

    public function test_it_supports_all_expected_options_with_defaults(
        $option = NULL,
        $expect_default = NULL,
        $custom_val = NULL
    ) {
        // it's not valid to have an empty data provider - this test is not relevant to this "FormElement" so
        // politely override it
        $this->addToAssertionCount(1);
    }

    public function test_it_has_all_child_elements()
    {
        $subject = $this->newSubject(
            [
                'elements' => [
                    ['type' => 'text', 'name' => 'email', 'label' => 'Email'],
                    ['type' => 'text', 'name' => 'detail[about][field]', 'label' => 'Detail'],
                ]
            ]
        );

        $this->assertFieldCollectionEquals(
            [
                ['class' => TextField::class, 'name' => 'email', 'value' => ''],
                ['class' => TextField::class, 'name' => 'detail[about][field]', 'value' => ''],
            ],
            $subject->elements
        );
    }

    #[TestWith([[], '', ''])]
    #[TestWith([['email' => 'foo@bar.net'], 'foo@bar.net', ''])]
    #[TestWith([['detail' => ['field' => 'stuff'], 'email' => 'foo@bar.net'], 'foo@bar.net', 'stuff'])]
    public function test_it_sets_all_child_element_values($value, $expect_1, $expect_2)
    {
        $subject = $this->newSubject(
            [
                'elements' => [
                    ['type' => 'text', 'name' => 'email', 'label' => 'Email'],
                    ['type' => 'text', 'name' => 'detail[field]', 'label' => 'Detail'],
                ]
            ]
        );
        $subject->setValues($value);

        $this->assertFieldCollectionEquals(
            [
                ['class' => TextField::class, 'name' => 'email', 'value' => $expect_1],
                ['class' => TextField::class, 'name' => 'detail[field]', 'value' => $expect_2],
            ],
            $subject->elements
        );
    }

    #[TestWith([[], ['email' => NULL, 'detail' => ['about' => ['field' => NULL]]]])]
    #[TestWith([['email' => 'foo@bar.net'], ['email' => 'foo@bar.net', 'detail' => ['about' => ['field' => NULL]]]])]
    public function test_it_returns_hash_of_child_element_domain_values($data, $expect)
    {
        $subject = $this->newSubject(
            [
                'elements' => [
                    ['type' => 'text', 'name' => 'email', 'label' => 'Email'],
                    ['type' => 'text', 'name' => 'detail[about][field]', 'label' => 'Detail'],
                ]
            ]
        );
        $subject->setValues($data);
        $this->assertSame($expect, $subject->getValues());
    }

    #[TestWith([[], [[], []]])]
    #[TestWith([['email' => ['Not an email'], 'detail' => ['about' => ['Invalid']]], [['Not an email'], ['Invalid']]])]
    public function test_it_assigns_errors_to_child_fields($errors, $expect)
    {
        $subject = $this->newSubject(
            [
                'elements' => [
                    ['type' => 'text', 'name' => 'email', 'label' => 'Email'],
                    ['type' => 'text', 'name' => 'detail[about]', 'label' => 'Detail'],
                ]
            ]
        );
        $subject->setErrors($errors);
        foreach ($subject->elements as $index => $element) {
            /** @var TextField $element */
            $this->assertSame($expect[$index], $element->errors);
        }
    }

    #[TestWith([[], FALSE])]
    #[TestWith([['email' => "That's not an email"], TRUE])]
    #[TestWith([['undefined' => 'No field for this one'], TRUE])]
    public function test_it_has_errors_if_any_errors_have_been_assigned($errors, $expect)
    {
        $subject = $this->newSubject(
            ['elements' => [['type' => 'text', 'name' => 'email', 'label' => 'Email']]]
        );
        $this->assertFalse($subject->has_errors, 'Should not have errors by default');
        $subject->setErrors($errors);
        $this->assertSame(
            $expect,
            $subject->has_errors,
            'Should match expected after errors assigned'
        );
    }

    public function test_it_assigns_global_errors_for_errors_that_are_not_attached_to_any_child()
    {
        $this->markTestIncomplete('This would be super-handy? Maybe need a way to list all kids');
    }

    public function test_it_supports_child_elements_without_values()
    {
        $subject = $this->newSubject(
            [
                'elements' => [
                    ['type' => 'body-text', 'content' => 'stuff'],
                ]
            ]
        );

        $subject->setErrors([]);
        $subject->setValues([]);
        $this->assertSame([], $subject->getValues());
    }

    protected function newSubject(array $values = []): Form
    {
        $default = [
            'elements' => [['type' => 'text', 'name' => 'foo', 'label' => 'foo']]
        ];

        return new Form(array_merge($default, $values), $this->getElementFactory());
    }

}

