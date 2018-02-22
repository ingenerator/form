<?php
/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */

namespace test\unit\Ingenerator\Form\Element;


use Ingenerator\Form\Element\Field\TextField;
use Ingenerator\Form\Element\FormGroupElement;
use Ingenerator\Form\Util\FormDataArray;

class FormGroupElementTest extends BaseFormElementTest
{
    public function provider_required_options()
    {
        return [
            ['label'],
            ['fields']
        ];
    }

    public function provider_valid_options_and_defaults()
    {
        $defaults   = parent::provider_valid_options_and_defaults();
        $defaults[] = ['container_data', [], ['data-showgroup', 'data-show' => 'stuff']];

        return $defaults;
    }

    public function test_it_has_label()
    {
        $this->assertSame('Information', $this->newSubject(['label' => 'Information'])->label);
    }

    public function test_it_has_all_child_elements()
    {
        $subject = $this->newSubject(
            [
                'fields' => [
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
            $subject->fields
        );
    }

    /**
     * @testWith [{}, "", ""]
     *           [{"email": "foo@bar.net"}, "foo@bar.net", ""]
     *           [{"detail": {"field": "stuff"}, "email": "foo@bar.net"}, "foo@bar.net", "stuff"]
     */
    public function test_it_assigns_values_to_all_child_elements($value, $expect_1, $expect_2)
    {
        $subject = $this->newSubject(
            [
                'fields' => [
                    ['type' => 'text', 'name' => 'email', 'label' => 'Email'],
                    ['type' => 'text', 'name' => 'detail[field]', 'label' => 'Detail'],
                ]
            ]
        );
        $subject->assignValue(new FormDataArray($value));

        $this->assertFieldCollectionEquals(
            [
                ['class' => TextField::class, 'name' => 'email', 'value' => $expect_1],
                ['class' => TextField::class, 'name' => 'detail[field]', 'value' => $expect_2],
            ],
            $subject->fields
        );
    }

    /**
     * @testWith [{}, {"email": null, "detail": {"about": {"field": null}}}]
     *           [{"email": "foo@bar.net"}, {"email": "foo@bar.net", "detail": {"about": {"field": null}}}]
     */
    public function test_it_collects_all_child_field_values($data, $expect)
    {
        $subject = $this->newSubject(
            [
                'fields' => [
                    ['type' => 'text', 'name' => 'email', 'label' => 'Email'],
                    ['type' => 'text', 'name' => 'detail[about][field]', 'label' => 'Detail'],
                ]
            ]
        );
        $subject->assignValue(new FormDataArray($data));
        $this->assertCollectsValues($expect, $subject);
    }

    public function test_it_supports_child_elements_without_values()
    {
        $subject = $this->newSubject(
            [
                'fields' => [
                    ['type' => 'body-text', 'content' => 'stuff'],
                ]
            ]
        );
        $subject->assignValue(new FormDataArray([]));
        $subject->assignErrors(new FormDataArray([]));
        $this->assertCollectsValues([], $subject);
    }

    /**
     * @testWith [{}, [[], []]]
     *           [{"email": ["Not an email"], "detail": {"about": ["Invalid"]}}, [["Not an email"],["Invalid"]]]
     */
    public function test_it_assigns_errors_to_child_fields($errors, $expect)
    {
        $subject = $this->newSubject(
            [
                'fields' => [
                    ['type' => 'text', 'name' => 'email', 'label' => 'Email'],
                    ['type' => 'text', 'name' => 'detail[about]', 'label' => 'Detail'],
                ]
            ]
        );
        $subject->assignErrors(new FormDataArray($errors));
        foreach ($subject->fields as $index => $field) {
            /** @var TextField $element */
            $this->assertSame($expect[$index], $field->errors);
        }
    }

    /**
     * @param array $values
     *
     * @return \Ingenerator\Form\Element\FormGroupElement
     */
    protected function newSubject(array $values = [])
    {
        $default = [
            'label'  => 'General',
            'fields' => [['type' => 'text', 'name' => 'foo', 'label' => 'foo']]
        ];

        return new FormGroupElement(array_merge($default, $values), $this->getElementFactory());
    }

}

