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

    #[TestWith([['constraints' => ['required']]])]
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
