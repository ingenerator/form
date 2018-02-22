<?php
/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */

namespace test\unit\Ingenerator\Form\Element\Field;


use Ingenerator\Form\Element\Field\ChoiceField;
use Ingenerator\Form\Element\Field\ChoiceOrOtherField;
use Ingenerator\Form\Element\Field\TextField;
use Ingenerator\Form\Util\FormDataArray;

class ChoiceOrOtherFieldTest extends BaseFieldTest
{

    public function test_it_is_initialisable()
    {
        $this->assertInstanceOf(ChoiceOrOtherField::class, $this->newSubject());
    }

    public function provider_required_options()
    {
        $required   = parent::provider_required_options();
        $required[] = ['choices'];
        $required[] = ['other_for_values'];

        return $required;
    }


    public function provider_valid_options_and_defaults()
    {
        $options   = parent::provider_valid_options_and_defaults();
        $options[] = ['length', NULL, 'short'];

        return $options;
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_it_throws_if_any_html5_constraints_specified()
    {
        $this->newSubject(['constraints' => ['required']]);
    }

    public function test_it_has_choice_subfield_with_expected_choices_and_options()
    {
        $subject = $this->newSubject(
            [
                'name'    => 'info',
                'label'   => 'Information',
                'choices' => ['One', 'Other'],
                'length'  => 'short'
            ]
        );
        $this->assertInstanceOf(\Ingenerator\Form\Element\Field\ChoiceField::class, $subject->choice_field);
        $this->assertSame('info[choice]', $subject->choice_field->name);
        $this->assertSame('short', $subject->choice_field->length);
        $this->assertSame('Information', $subject->choice_field->label);

        $this->assertEquals(
            (new ChoiceField(
                ['name' => 'f', 'label' => 'f', 'choices' => ['One', 'Other']]
            ))->choices,
            $subject->choice_field->choices
        );
    }

    public function test_it_has_text_subfield_for_detail()
    {
        $subject = $this->newSubject(
            [
                'name'    => 'info',
                'label'   => 'Information',
                'choices' => ['One', 'Other'],
                'length'  => 'medium'
            ]
        );
        $this->assertInstanceOf(TextField::class, $subject->detail_field);
        $this->assertSame('info[detail]', $subject->detail_field->name);
        $this->assertSame('medium', $subject->detail_field->length);
        $this->assertSame('Information (Other)', $subject->detail_field->label);
    }

    public function test_its_value_is_empty_by_default()
    {
        $subject = $this->newSubject();
        $this->assertSame('', $subject->choice_field->html_value, 'Choice should be empty');
        $this->assertSame('', $subject->detail_field->html_value, 'Detail should be empty');
    }

    /**
     * @testWith [{}, {"choice":"", "detail": ""}]
     *           [{"choice": "One"}, {"choice":"One", "detail": ""}]
     *           [{"choice": "One", "detail": "Other"}, {"choice":"One", "detail": "Other"}]
     */
    public function test_it_assigns_values_to_subfields($values, $expect)
    {
        $subject = $this->newSubject(
            ['name' => 'field', 'choices' => ['One', 'Two'], 'other_for_values' => ['Other']]
        );
        $subject->assignValue(new FormDataArray(['field' => $values]));
        $this->assertSame(
            $expect,
            [
                'choice' => $subject->choice_field->html_value,
                'detail' => $subject->detail_field->html_value
            ]
        );
    }

    /**
     * @testWith [{}, {"choice":null, "detail": null}]
     *           [{"choice": "One"}, {"choice":"One", "detail": null}]
     *           [{"choice": "Other", "detail": "Red"}, {"choice":"Other", "detail": "Red"}]
     *           [{"choice": "Two", "detail": "Red"}, {"choice":"Two", "detail": null}]
     */
    public function test_it_collects_choice_and_detail_field_values($values, $expect)
    {
        $subject = $this->newSubject(
            ['name' => 'field', 'choices' => ['One', 'Two'], 'other_for_values' => ['Other']]
        );
        $subject->assignValue(new FormDataArray(['field' => $values]));
        $this->assertCollectsValues(['field' => $expect], $subject);
    }

    public function test_it_optionally_supports_combining_choice_and_detail_as_single_value()
    {
        $this->markTestIncomplete();
    }

    public function test_what_happens_about_other_schema_values_that_dont_work_for_this_type()
    {
        $this->markTestIncomplete('What about the top-level label, empty value, other properties?');
    }

    /**
     * @param array $values
     *
     * @return \Ingenerator\Form\Element\Field\ChoiceOrOtherField
     */
    protected function newSubject(array $values = [])
    {
        $default = [
            'name'             => 'foofield',
            'label'            => 'What\'s the best foo?',
            'choices'          => ['One', 'Other'],
            'other_for_values' => ['Other']
        ];

        return new \Ingenerator\Form\Element\Field\ChoiceOrOtherField(array_merge($default, $values), $this->getElementFactory());
    }

}



