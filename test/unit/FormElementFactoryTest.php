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
use Ingenerator\Form\FormConfig;
use Ingenerator\Form\FormElementFactory;
use Ingenerator\Form\UndefinedFieldTypeException;
use InvalidArgumentException;

class FormElementFactoryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Ingenerator\Form\FormConfig
     */
    protected $config;

    public function test_it_is_initialisable()
    {
        $this->assertInstanceOf(FormElementFactory::class, $this->newSubject());
    }

    public function test_it_throws_if_field_type_is_empty()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->newSubject()->make([[]]);
    }

    public function test_it_throws_if_field_type_is_not_mapped()
    {
        $this->config = FormConfig::withDefaults(['element_type_map' => ['text' => NULL]]);
        $this->expectException(UndefinedFieldTypeException::class);
        $this->newSubject()->make([['type' => 'text']]);
    }

    public function test_it_makes_empty_array_from_empty_array()
    {
        $this->assertSame([], $this->newSubject()->make([]));
    }

    public function test_it_makes_single_instance_of_mapped_type()
    {
        $this->config = FormConfig::withDefaults(
            ['element_type_map' => ['text' => TextField::class]]
        );
        $elements     = $this->newSubject()->make(
            [
                ['type' => 'text', 'name' => 'email', 'label' => 'Email']
            ]
        );
        $this->assertCount(1, $elements);
        $this->assertFieldInstanceNamed(TextField::class, 'email', $elements[0]);
    }

    public function test_it_makes_array_of_instances_of_mapped_types()
    {
        $this->config = FormConfig::withDefaults(
            ['element_type_map' => ['text' => TextField::class, 'textarea' => TextareaField::class]]
        );
        $elements     = $this->newSubject()->make(
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
        $this->config = FormConfig::withDefaults();
        $elements     = $this->newSubject()->make(
            [['type' => 'rough-date-range', 'name' => 'dates']]
        );
        $this->assertCount(1, $elements);
        $this->assertFieldInstanceNamed(RoughDateRangeField::class, 'dates', $elements[0]);
    }

    public function setUp(): void
    {
        parent::setUp();
        $this->config = FormConfig::withDefaults();
    }
    
    /**
     * @return \Ingenerator\Form\FormElementFactory
     */
    protected function newSubject()
    {
        return new FormElementFactory($this->config);
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
