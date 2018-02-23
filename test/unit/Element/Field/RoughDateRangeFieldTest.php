<?php
/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */

namespace test\unit\Ingenerator\Form\Element\Field;


use Ingenerator\Form\Element\Field\RoughDateRangeField;
use Ingenerator\Form\Element\Field\TextField;
use Ingenerator\Form\Util\FormDataArray;

class RoughDateRangeFieldTest extends BaseFieldTest
{

    public function test_it_is_initialisable()
    {
        $this->assertInstanceOf(RoughDateRangeField::class, $this->newSubject());
    }

    public function provider_required_options()
    {
        return [
            ['name']
        ];
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_it_throws_if_any_html5_constraints_specified()
    {
        $this->newSubject(['constraints' => ['required']]);
    }

    /**
     * @testWith ["from_field", "Date From", "field[from]"]
     *           ["to_field", "Date To", "field[to]"]
     */
    public function test_it_has_child_text_field_for_date_from_and_date_to(
        $child,
        $expect_label,
        $expect_name
    ) {
        $field = $this->newSubject(['name' => 'field'])->$child;
        $this->assertInstanceOf(\Ingenerator\Form\Element\Field\TextField::class, $field);
        /** @var TextField $field */
        $this->assertSame($expect_name, $field->name);
        $this->assertSame($expect_label, $field->label);
    }

    /**
     * @testWith [{}, {"from": "", "to": ""}]
     *           [{"field": {"from": "October 2012", "to": "Jan 2016"}}, {"from": "October 2012", "to": "Jan 2016"}]
     */
    public function test_it_assigns_subfield_values($post, $expect)
    {
        $subject = $this->newSubject(['name' => 'field']);
        $subject->assignValue(new FormDataArray($post));
        $this->assertSame($expect['from'], $subject->from_field->html_value);
        $this->assertSame($expect['to'], $subject->to_field->html_value);
    }

    /**
     * @testWith [{}, {"from": null, "to": null}]
     *           [{"field": {"from": "October 2012", "to": "Jan 2016"}}, {"from": "October 2012", "to": "Jan 2016"}]
     */
    public function test_it_collects_values_for_from_and_to_subfields($post, $expect)
    {
        $subject = $this->newSubject(['name' => 'field']);
        $subject->assignValue(new FormDataArray($post));
        $this->assertCollectsValues(['field' => $expect], $subject);
    }

    /**
     * @testWith [{"from": null, "to": null}, null]
     *           [{"from": "October 2012", "to": "Jan 2016"}, "October 2012 - Jan 2016"]
     *           [{"from": "October 2012", "to": null}, "October 2012"]
     *           [{"to": "Jan 2016"}, "Jan 2016"]
     */
    public function test_it_hyphenates_range_as_display_value($post, $expect)
    {
        $subject = $this->newSubject(['name' => 'field']);
        $subject->assignValue(new FormDataArray(['field' => $post]));
        $this->assertSame($expect, $subject->display_value);
    }

    public function test_what_happens_about_other_schema_values_that_dont_work_for_this_type()
    {
        $this->markTestIncomplete('What about the top-level label, empty value, other properties?');
    }

    /**
     * @param array $values
     *
     * @return \Ingenerator\Form\Element\Field\RoughDateRangeField
     */
    protected function newSubject(array $values = [])
    {
        $default = [
            'name' => 'foofield',
        ];

        return new RoughDateRangeField(array_merge($default, $values), $this->getElementFactory());
    }
}
