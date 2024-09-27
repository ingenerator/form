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
            $this->newSubject()
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

    public static function provider_field_values(): array
    {
        return [
            [NULL, '', NULL],
            [
                new DateTimeImmutable('2017-02-03'),
                '2017-02-03',
                new DateTimeImmutable('2017-02-03')
            ],
            ['2017-02-03', '2017-02-03', new DateTimeImmutable('2017-02-03 00:00:00')],
            ['2017-02-32', '2017-02-32', new InvalidUserDateTime('2017-02-32')],
            ['2017-13-03', '2017-13-03', new InvalidUserDateTime('2017-13-03')],
            ['invalid', 'invalid', new InvalidUserDateTime('invalid')]
        ];
    }

    #[DataProvider('provider_field_values')]
    public function test_it_accepts_incoming_datetime_immutable_or_string_value(
        $val,
        $expect_html,
        $expect_internal
    ) {
        $subject = $this->newSubject(['name' => 'field']);
        $subject->assignValue(new FormDataArray(['field' => $val]));
        $this->assertSame($expect_html, $subject->html_value, 'Should have expected html value');

        $data = new FormDataArray([]);
        $subject->collectValue($data);
        if ($expect_internal instanceof InvalidUserDateTime) {
            $this->assertInstanceOf(InvalidUserDateTime::class, $data->getValues()['field']);
            $this->assertSame((string) $expect_internal, (string) $data->getValues()['field']);
        } elseif ($expect_internal instanceof DateTimeImmutable) {
            $this->assertEquals(
                $expect_internal,
                $data->getValues()['field'],
                'Should have expected domain value'
            );
        } else {
            $this->assertSame(
                $expect_internal,
                $data->getValues()['field'],
                'Should have expected domain value'
            );
        }
    }

    public static function provider_date_values_for_display(): array
    {
        return [
            [NULL, NULL],
            ['2018-02-02', '2 Feb 2018'],
            [new DateTimeImmutable('2018-11-13'), '13 Nov 2018']
        ];
    }

    #[DataProvider('provider_date_values_for_display')]
    public function test_it_assigns_display_value_as_day_month_year($value, $expect)
    {
        $subject = $this->newSubject(['name' => 'field']);
        $subject->assignValue(new FormDataArray(['field' => $value]));
        $this->assertEquals($expect, $subject->display_value);
    }

    protected function newSubject(array $values = []): DateField
    {
        $default = [
            'name'  => 'foofield',
            'label' => 'What\'s the best foo?',
        ];

        return new DateField(array_merge($default, $values));
    }

}
