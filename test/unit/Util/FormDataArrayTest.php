<?php
/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */

namespace test\unit\Ingenerator\Form\Util;


use Ingenerator\Form\Util\FormDataArray;
use InvalidArgumentException;
use LogicException;
use UnexpectedValueException;

class FormDataArrayTest extends \PHPUnit\Framework\TestCase
{

    public function test_it_is_initialisable()
    {
        $this->assertInstanceOf(FormDataArray::class, $this->newSubject([]));
    }

    public function test_it_returns_null_value_for_any_unknown_field()
    {
        $subject = $this->newSubject([]);
        $this->assertNull($subject->getRawValue('foo'), 'Valid empty value for simple field');
        $this->assertNull(
            $subject->getRawValue('foo[bar]', 'Valid empty value for nested array field')
        );
        $this->assertNull(
            $subject->getRawValue('foo[bar][baz]', 'Valid empty value for nested array field')
        );
    }

    public function test_it_returns_raw_value_for_simple_field()
    {
        $subject = $this->newSubject(['foo' => 'bar']);

        $this->assertEquals('bar', $subject->getRawValue('foo'));
    }

    public function test_it_returns_raw_value_for_single_nested_field()
    {
        $subject = $this->newSubject(['foo' => ['bar' => 'value']]);

        $this->assertEquals('value', $subject->getRawValue('foo[bar]'));
    }

    public function test_it_returns_raw_value_for_deep_nested_field()
    {
        $subject = $this->newSubject(['foo' => ['bar' => ['baz' => ['biz' => 'value']]]]);

        $this->assertEquals('value', $subject->getRawValue('foo[bar][baz][biz]'));
    }

    public function test_it_returns_raw_value_for_deep_nested_field_with_integer_key()
    {
        $subject = $this->newSubject(['foo' => ['bar' => [['biz' => 'value']]]]);

        $this->assertEquals('value', $subject->getRawValue('foo[bar][0][biz]'));
    }

    public function provider_invalid_fieldnames()
    {
        return [
            ['foo[bar[bazl]'],
            ['foo[bar[bazl]]']
        ];
    }

    /**
     * @dataProvider provider_invalid_fieldnames
     */
    public function test_it_throws_from_raw_value_with_invalid_fieldname($name)
    {
        $this->expectException(InvalidArgumentException::class);
        $this->newSubject([])->getRawValue($name);
    }

    /**
     * @dataProvider provider_invalid_fieldnames
     */
    public function test_it_throws_when_setting_invalid_fieldname($name)
    {
        $this->expectException(InvalidArgumentException::class);
        $this->newSubject([])->setFieldValue($name, NULL);
    }

    /**
     * @testWith ["foo[bar]"]
     *           ["foo[bar][bex]"]
     *           ["foo[bar][bex][data]"]
     */
    public function test_it_throws_if_attempting_to_reassign_existing_key($bad_path)
    {
        $subject = $this->newSubject([]);
        $subject->setFieldValue('foo[bar][bex]', 'anything');
        $this->expectException(LogicException::class);
        $subject->setFieldValue($bad_path, 'oh dear');
    }

    public function test_it_can_set_value_for_single_nested_field()
    {
        $subject = $this->newSubject([]);
        $subject->setFieldValue('foo', 'bar');
        $this->assertSame(['foo' => 'bar'], $subject->getValues());
    }

    public function test_it_can_set_value_for_deep_nested_field()
    {
        $subject = $this->newSubject([]);
        $subject->setFieldValue('foo[bar][baz][biz]', 'anything');
        $this->assertSame(
            ['foo' => ['bar' => ['baz' => ['biz' => 'anything']]]],
            $subject->getValues()
        );
    }

    public function test_it_can_set_value_for_deep_nested_field_with_integer_key()
    {
        $subject = $this->newSubject([]);
        $subject->setFieldValue('foo[bar][9][biz]', 'anything');
        $this->assertSame(
            ['foo' => ['bar' => [9 => ['biz' => 'anything']]]],
            $subject->getValues()
        );
    }

    public function test_it_can_set_multiple_values_in_same_array()
    {
        $subject = $this->newSubject([]);
        $subject->setFieldValue('foo', 'stuff');
        $subject->setFieldValue('bar[biz]', 'other');
        $subject->setFieldValue('bar[boz]', 'new');
        $this->assertSame(
            ['foo' => 'stuff', 'bar' => ['biz' => 'other', 'boz' => 'new']],
            $subject->getValues()
        );
    }

    public function test_it_never_matches_missing_value()
    {
        $subject = $this->newSubject([]);
        $this->assertFalse($subject->matchesValue('foo[bar]', 'stuff'));
    }

    public function test_it_does_not_match_value_when_different()
    {
        $subject = $this->newSubject(['choice' => ['value' => 'selected']]);
        $this->assertFalse($subject->matchesValue('choice[value]', 'different'));
    }

    public function test_it_matches_value_when_the_same()
    {
        $subject = $this->newSubject(['choice' => ['value' => 'selected']]);
        $this->assertTrue($subject->matchesValue('choice[value]', 'selected'));
    }

    public function test_it_matches_value_when_equivalent()
    {
        $subject = $this->newSubject(['choice' => ['value' => 1]]);
        $this->assertTrue($subject->matchesValue('choice[value]', '1'));
    }

    public function test_it_has_empty_group_indices_for_unknown_field()
    {
        $subject = $this->newSubject([]);
        $this->assertEquals([], $subject->getGroupIndices('foo[bar]'));
    }

    public function test_it_has_empty_group_indices_for_empty_array_field()
    {
        $subject = $this->newSubject(['foo' => ['bar' => []]]);
        $this->assertEquals([], $subject->getGroupIndices('foo[bar]'));
    }

    public function test_it_has_group_indices_of_indexed_array_field()
    {
        $subject = $this->newSubject(['foo' => ['element_zero', 'element_one']]);
        $this->assertEquals([0, 1], $subject->getGroupIndices('foo'));
    }

    public function test_it_has_group_indices_of_assoc_array_field()
    {
        $subject = $this->newSubject(['foo' => ['zero' => '0', 'one' => '1']]);
        $this->assertEquals(['zero', 'one'], $subject->getGroupIndices('foo'));
    }

    public function test_it_throws_for_group_indices_of_non_array_field()
    {
        $this->expectException(UnexpectedValueException::class);
        $this->newSubject(['foo' => 'bar'])->getGroupIndices('foo');
    }

    public function test_it_returns_empty_string_for_selected_when_value_does_not_match()
    {
        $this->assertEquals(
            '',
            $this->newSubject(['foo' => 'bar'])->isSelected('foo', 'other')
        );
    }

    public function test_it_returns_selected_when_value_matches()
    {
        $this->assertEquals(
            'selected',
            $this->newSubject(['foo' => 'bar'])->isSelected('foo', 'bar')
        );
    }

    /**
     * @param array $data
     *
     * @return FormDataArray
     */
    protected function newSubject(array $data)
    {
        return new FormDataArray($data);
    }
}
