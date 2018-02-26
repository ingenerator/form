<?php
/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */

namespace test\unit\Ingenerator\Form\Helper;


use Ingenerator\Form\Util\ShowgroupHelper;

class ShowgroupHelperTest extends \PHPUnit_Framework_TestCase {

    protected $value;

    public function test_it_is_initialisable()
    {
        $this->assertInstanceOf(ShowgroupHelper::class, $this->newSubject());
    }

    public function test_it_returns_data_showgroups_tags_for_single_value()
    {
        $this->assertRegExp(
            '/(^| )data-showgroups="value"( |$)/',
            $this->newSubject()->attrsForGroup(['value'])
        );
    }

    public function test_it_returns_data_showgroups_attribute_for_multiple_values()
    {
        $this->assertRegExp(
            '/(^| )data-showgroups="one,two"( |$)/',
            $this->newSubject()->attrsForGroup(['one', 'two'])
        );
    }

    public function test_it_renders_no_style_attribute_when_element_should_display_for_current_value()
    {
        $this->value = 'selected';
        $this->assertNotContains(
            'style',
            $this->newSubject()->attrsForGroup(['selected'])
        );
    }

    public function test_it_renders_style_display_none_when_element_should_not_display_for_current_value()
    {
        $this->value = 'not-selected';
        $this->assertContains(
            ' style="display:none;"',
            $this->newSubject()->attrsForGroup(['selected'])
        );
    }

    protected function newSubject()
    {
        return new ShowgroupHelper($this->value);
    }

}
