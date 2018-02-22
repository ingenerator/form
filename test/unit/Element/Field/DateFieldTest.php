<?php
/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */

namespace test\unit\Ingenerator\Form\Element\Field;


use Ingenerator\Form\Util\FormDataArray;
use Ingenerator\PHPUtils\DateTime\InvalidUserDateTime;

class DateFieldTest extends BaseFieldTest
{
    public function test_it_is_initialisable_from_schema_array()
    {
        $this->assertInstanceOf(
            \Ingenerator\Form\Element\Field\DateField::class,
            $this->newSubject()
        );
    }

    /**
     * @testWith [{"constraints": ["required"]}]
     */
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

    public function provider_field_values()
    {
        return [
            [NULL, '', NULL],
            [
                new \DateTimeImmutable('2017-02-03'),
                '2017-02-03',
                new \DateTimeImmutable('2017-02-03')
            ],
            ['2017-02-03', '2017-02-03', new \DateTimeImmutable('2017-02-03 00:00:00')],
            ['2017-02-32', '2017-02-32', new InvalidUserDateTime('2017-02-32')],
            ['2017-13-03', '2017-13-03', new InvalidUserDateTime('2017-13-03')],
            ['invalid', 'invalid', new InvalidUserDateTime('invalid')]
        ];
    }

    /**
     * @dataProvider provider_field_values
     */
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
        } elseif ($expect_internal instanceof \DateTimeImmutable) {
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

    /**
     * @param array $values
     *
     * @return \Ingenerator\Form\Element\Field\DateField
     */
    protected function newSubject(array $values = [])
    {
        $default = [
            'name'  => 'foofield',
            'label' => 'What\'s the best foo?',
        ];

        return new \Ingenerator\Form\Element\Field\DateField(array_merge($default, $values));
    }

}
