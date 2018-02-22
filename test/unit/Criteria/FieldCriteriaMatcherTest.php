<?php
/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */

namespace test\unit\Teamdetails\Form\Criteria;


use Ingenerator\Form\Criteria\FieldCriteriaMatcher;

class FieldCriteriaMatcherTest extends \PHPUnit_Framework_TestCase {

    public function test_it_is_initialisable()
    {
        $this->assertInstanceOf(FieldCriteriaMatcher::class, $this->newSubject());
    }

    public function test_it_does_not_match_anything_with_no_criteria()
    {
        $this->newSubject()->matches('foo', []);
    }

    /**
     * @param string $empty_value
     * @dataProvider provider_empty_values
     */
    public function test_it_does_not_match_not_empty_criteria_with_empty_value($empty_value)
    {
        $this->assertFalse($this->newSubject()->matches($empty_value, ['not_empty']));
    }

    /**
     * @param string $not_empty_value
     * @dataProvider provider_not_empty_values
     */
    public function test_it_matches_not_empty_criteria_with_not_empty_value($not_empty_value)
    {
        $this->assertTrue($this->newSubject()->matches($not_empty_value, ['not_empty']));
    }

    public function test_it_matches_exact_value()
    {
        $this->assertTrue($this->newSubject()->matches('Yes', ['value:Yes']));
    }

    public function test_it_matches_exact_value_containing_colon()
    {
        $this->assertTrue($this->newSubject()->matches('some:thing', ['value:some:thing']));
    }

    public function test_it_does_not_match_exact_value()
    {
        $this->assertFalse($this->newSubject()->matches('Yes', ['value:No']));
    }

    public function test_it_matches_array_of_criteria_when_one_criteria_passes()
    {
        $subject = $this->newSubject();
        $this->assertTrue($subject->matches('', ['value:Yes', 'empty']), 'Should match ""');
        $this->assertTrue($subject->matches('Yes', ['value:Yes', 'empty']), 'Should match "Yes"');
    }

    public function test_it_does_not_match_array_of_criteria_if_all_criteria_fail()
    {
        $subject = $this->newSubject();
        $this->assertFalse($subject->matches('No', ['value:Yes', 'empty']), 'Should not match "No"');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_it_throws_with_unknown_criteria_type()
    {
        $this->newSubject()->matches('stuff', ['random']);
    }

    public function provider_not_empty_values()
    {
        return [
            ['foo'],
            ['0'],
            [0],
        ];
    }

    public function provider_empty_values()
    {
        return [
            [NULL],
            [''],
            [' '],
        ];
    }


    protected function newSubject()
    {
        return new FieldCriteriaMatcher();
    }
}
