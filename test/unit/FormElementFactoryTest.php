<?php
/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */

namespace test\unit\Ingenerator\Form;


use Ingenerator\Form\Element\Field\AbstractFormField;
use Ingenerator\Form\Element\Field\RoughDateRangeField;
use Ingenerator\Form\Element\Field\TextareaField;
use Ingenerator\Form\Element\Field\TextField;
use Ingenerator\Form\FormElementFactory;

class FormElementFactoryTest extends \PHPUnit_Framework_TestCase
{
    protected $mapping = [];

    public function test_it_is_initialisable()
    {
        $this->assertInstanceOf(FormElementFactory::class, $this->newSubject());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_it_throws_if_field_type_is_empty()
    {
        $this->newSubject()->make([[]]);
    }

    /**
     * @expectedException \OutOfBoundsException
     */
    public function test_it_throws_if_field_type_is_not_mapped()
    {
        $this->mapping = [];
        $this->newSubject()->make([['type' => 'text']]);
    }

    public function test_it_makes_empty_array_from_empty_array()
    {
        $this->assertSame([], $this->newSubject()->make([]));
    }

    public function test_it_makes_single_instance_of_mapped_type()
    {
        $this->mapping = ['text' => TextField::class];
        $elements      = $this->newSubject()->make(
            [
                ['type' => 'text', 'name' => 'email', 'label' => 'Email']
            ]
        );
        $this->assertCount(1, $elements);
        $this->assertFieldInstanceNamed(TextField::class, 'email', $elements[0]);
    }

    public function test_it_makes_array_of_instances_of_mapped_types()
    {
        $this->mapping = ['text' => TextField::class, 'textarea' => TextareaField::class];
        $elements      = $this->newSubject()->make(
            [
                ['type' => 'text', 'name' => 'email', 'label' => 'Email'],
                ['type' => 'textarea', 'name' => 'info', 'label' => 'Information']
            ]
        );
        $this->assertCount(2, $elements);
        $this->assertFieldInstanceNamed(TextField::class, 'email', $elements[0]);
        $this->assertFieldInstanceNamed(TextareaField::class, 'info', $elements[1]);
    }

    public function test_it_makes_instances_of_elements_that_need_the_factory()
    {
        $this->mapping = [
            'text'             => TextField::class,
            'rough-date-range' => RoughDateRangeField::class
        ];
        $elements      = $this->newSubject()->make(
            [['type' => 'rough-date-range', 'name' => 'dates']]
        );
        $this->assertCount(1, $elements);
        $this->assertFieldInstanceNamed(RoughDateRangeField::class, 'dates', $elements[0]);
    }

    /**
     * @return \Ingenerator\Form\FormElementFactory
     */
    protected function newSubject()
    {
        return new FormElementFactory($this->mapping);
    }

    protected function assertFieldInstanceNamed(
        $expect_class,
        $expect_name,
        AbstractFormField $field
    ) {
        $this->assertInstanceOf($expect_class, $field);
        $this->assertEquals($expect_name, $field->name);
    }
}
