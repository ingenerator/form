<?php
/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */

namespace test\unit\Ingenerator\Form\Element;


use Ingenerator\Form\FormConfig;
use Ingenerator\Form\FormElementFactory;
use Ingenerator\Form\Util\FormDataArray;

abstract class BaseFormElementTest extends \PHPUnit_Framework_TestCase
{

    public function provider_required_options()
    {
        return [];
    }

    public function provider_valid_options_and_defaults()
    {
        return [];
    }

    /**
     * @dataProvider provider_required_options
     * @expectedException \DomainException
     */
    public function test_it_cannot_be_constructed_without_required_options($option)
    {
        $this->newSubject([$option => '']);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_it_cannot_be_constructed_with_invalid_options()
    {
        $this->newSubject(['some-old-nonsense' => 'junk']);
    }

    /**
     * @expectedException \OutOfBoundsException
     */
    public function test_it_throws_on_access_to_unknown_property()
    {
        $this->newSubject()->some_old_nonsense;
    }

    /**
     * @expectedException \LogicException
     */
    public function test_it_throws_on_attempt_to_assign_any_property()
    {
        $field = 'name';

        $this->newSubject()->$field = 'cannot-do-this';
    }

    /**
     * @dataProvider provider_valid_options_and_defaults
     */
    public function test_it_supports_all_expected_options_with_defaults(
        $option,
        $expect_default,
        $custom_val
    ) {
        $this->assertSame(
            $expect_default,
            $this->newSubject()->$option,
            'Provides expected default option'
        );
        $this->assertSame(
            $custom_val,
            $this->newSubject([$option => $custom_val])->$option,
            'Provides expected custom option'
        );
    }

    public function test_it_ignores_comments()
    {
        // NB: removing this from the array might be problematic if we want to persist a collection
        // of field schemas back to an array....
        $subject = $this->newSubject(['_comment' => 'Some info to make the schema make sense']);
        try {
            $foo = $subject->_comment;
            $this->fail('Should throw on attempt to access a _comment property');
        } catch (\OutOfBoundsException $e) {
            // Expected
        }
    }

    public function test_it_ignores_type_schema()
    {
        // NB: removing this from the array might be problematic if we want to persist a collection
        // of field schemas back to an array....
        $subject = $this->newSubject(['type' => 'text']);
        try {
            $foo = $subject->type;
            $this->fail("Should throw on attempt to access a type property");
        } catch (\OutOfBoundsException $e) {
            // Expected
        }
    }

    /**
     * @param array $values
     *
     * @return AbstractFormElement
     */
    abstract protected function newSubject(array $values = []);

    /**
     * @param                     $expect
     * @param \Ingenerator\Form\Element\Field\AbstractFormField[] $elements
     */
    protected function assertFieldCollectionEquals(array $expect, array $elements)
    {
        $actual = [];
        foreach ($elements as $index => $field) {
            $actual[$index] = [
                'class' => get_class($field),
                'name'  => $field->name,
                'value' => $field->html_value
            ];
        }
        $this->assertEquals($expect, $actual);
    }

    /**
     * @param $expect
     * @param $subject
     */
    protected function assertCollectsValues(array $expect, \Ingenerator\Form\Element\FormValueElement $subject)
    {
        $data = new FormDataArray([]);
        $subject->collectValue($data);
        $this->assertSame($expect, $data->getValues());
    }

    /**
     * @return \Ingenerator\Form\FormElementFactory
     */
    protected function getElementFactory()
    {
        return new FormElementFactory(FormConfig::withDefaults());
    }
}
