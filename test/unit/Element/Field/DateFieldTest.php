<?php
/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */

namespace test\unit\Ingenerator\Form\Element\Field;


use DateTimeImmutable;
use Ingenerator\Form\Element\Field\DateField;
use Ingenerator\Form\TestSupport\PHPUnit10\BaseFieldTestCase;
use Ingenerator\Form\Util\FormDataArray;
use Ingenerator\PHPUtils\DateTime\InvalidUserDateTime;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestWith;
use function array_merge;

class DateFieldTest extends BaseFieldTestCase
{
    public function test_it_is_initialisable_from_schema_array()
    {
        $this->assertInstanceOf(
            DateField::class,
            $this->newSubject(),
        );
    }

    public static function providerHtml5Constraints(): iterable
    {
        yield 'valid with no constraints' => [
            [],
            false,
        ];

        yield 'validate with arbitrary custom constraints' => [
            ['some-other-attr' => 'whatever'],
            false
        ];

        yield 'valid with required' => [
            ['required'],
            false,
        ];

        yield 'valid with valid min' => [
            ['min' => '2026-03-04'],
            false,
        ];

        yield 'valid with valid max' => [
            ['max' => '2026-09-01'],
            false,
        ];

        yield 'valid with required, min, max' => [
            ['min' => '2026-03-04', 'max' => '2026-04-09', 'required'],
            false,
        ];

        yield 'valid with same min & max' => [
            // Why you'd need this (basically forces the field to a single date) is unclear but it is a valid constraint
            ['min' => '2026-03-04', 'max' => '2026-03-04'],
            false,
        ];


        yield 'throws with invalid `min`' => [
            ['min' => '2026-02-31'],
            'min constraint must be a valid Y-m-d date string, got 2026-02-31 for some-field',
        ];

        yield 'throws with invalid `max`' => [
            ['max' => '03/02/2026'],
            'max constraint must be a valid Y-m-d date string, got 03/02/2026 for some-field',
        ];

        yield 'throws if `max` is before `min`' => [
            ['min' => '2026-01-02', 'max' => '2026-01-01'],
            'max must be greater than min (got min=2026-01-01, max=2026-01-02 for some-field)',
        ];
    }

    #[DataProvider('providerHtml5Constraints')]
    public function testItAcceptsAndValidatesHtml5Constraints(array $constraints, false|string $expectThrows): void
    {
        if ($expectThrows !== false) {
            $this->expectException(InvalidArgumentException::class);
            $this->expectExceptionMessage($expectThrows);
        }

        $subject = $this->newSubject(['constraints' => $constraints, 'name' => 'some-field']);

        if ($expectThrows === false) {
            $this->assertSame($subject->constraints, $constraints);
        }
    }

    public function test_its_default_html_value_is_empty_string()
    {
        $this->assertSame('', $this->newSubject()->html_value);
    }

    /**
     * @return iterable<string, array{null|DateTimeImmutable|string, string, ?string}>
     */
    public static function provider_field_values(): iterable
    {
        yield 'assign null' => [
            NULL,
            '',
            NULL,
        ];

        yield 'assign DateTimeImmutable' => [
            new DateTimeImmutable('2017-02-03'),
            '2017-02-03',
            '2017-02-03',
        ];

        yield 'assign string with nonexistent ymd date' => [
            '2017-02-32',
            '2017-02-32',
            '2017-02-32',
        ];

        yield 'assign string with invalid format' => [
            'invalid',
            'invalid',
            'invalid',
        ];
    }

    #[DataProvider('provider_field_values')]
    public function test_it_accepts_incoming_datetime_immutable_or_string_value_and_provides_as_ymd_string(
        DateTimeImmutable|string|null $val,
        string                        $expect_html,
        ?string                       $expect_internal
    )
    {
        $subject = $this->newSubject(['name' => 'field']);
        $subject->assignValue(new FormDataArray(['field' => $val]));
        $this->assertSame($expect_html, $subject->html_value, 'Should have expected html value');

        $data = new FormDataArray([]);
        $subject->collectValue($data);
        $this->assertSame(
            $expect_internal,
            $data->getValues()['field'],
            'Should have expected domain value',
        );
    }

    /**
     * @return iterable<string, array{DateTimeImmutable|string|null, string, ?array<string, mixed>}>
     */
    public static function provider_date_values_for_display(): iterable
    {
        yield 'null displays as `null` by default' => [
            null,
            null,
        ];

        yield 'empty string displays as `null`' => [
            '',
            null,
        ];

        yield 'empty displays custom empty_value' => [
            '',
            'Not set',
            ['empty_value' => 'Not set'],
        ];

        yield 'valid Y-m-d string displays expected format' => [
            '2018-02-02',
            '2 Feb 2018',
        ];

        yield 'valid DateTimeImmutable displays expected format' => [
            new DateTimeImmutable('2018-11-13'),
            '13 Nov 2018',
        ];

        yield 'Y-m-d string for nonexistent date displays with "invalid" marker' => [
            '2028-02-31',
            'Invalid date (2028-02-31)',
        ];

        yield 'Non Y-m-d string format displays with "invalid" marker' => [
            '07/02/2026',
            'Invalid date (07/02/2026)',
        ];

        yield 'Nonsense string format displays with "invalid" marker' => [
            'rubbish',
            'Invalid date (rubbish)',
        ];
    }

    #[DataProvider('provider_date_values_for_display')]
    public function test_it_assigns_display_value_as_day_month_year(
        DateTimeImmutable|string|null $value,
        ?string                       $expect,
        array                         $options = [],
    )
    {
        $subject = $this->newSubject(['name' => 'field', ...$options]);
        $subject->assignValue(new FormDataArray(['field' => $value]));
        $this->assertEquals($expect, $subject->display_value);
    }

    protected function newSubject(array $values = []): DateField
    {
        $default = [
            'name' => 'foofield',
            'label' => 'What\'s the best foo?',
        ];

        return new DateField(array_merge($default, $values));
    }

}
