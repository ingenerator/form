<?php
/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */

namespace test\unit\Ingenerator\Form\Renderer;


use Ingenerator\Form\Criteria\FieldCriteriaMatcher;
use Ingenerator\Form\Element\BodyTextFormElement;
use Ingenerator\Form\Element\Field\ChoiceField;
use Ingenerator\Form\Element\Field\TextField;
use Ingenerator\Form\Element\FormGroupElement;
use Ingenerator\Form\Form;
use Ingenerator\Form\FormConfig;
use Ingenerator\Form\FormElementFactory;
use Ingenerator\Form\Renderer\FormElementRenderer;

class FormElementRendererTest extends \PHPUnit\Framework\TestCase
{

    /**
     * @var \Ingenerator\Form\FormConfig
     */
    protected $config;

    /**
     * @var string
     */
    protected $render_mode = 'edit';

    public function test_it_is_initialisable()
    {
        $this->assertInstanceOf(FormElementRenderer::class, $this->newSubject());
    }

    public function test_its_constraint_attribute_helper_renders_as_escaped_attributes()
    {
        $field = new TextField(
            [
                'label'       => 'anything',
                'name'        => 'anything',
                'constraints' => ['min' => 'bar', 'pattern' => '>"it', 'required']
            ]
        );

        $this->assertEquals(
            ' min="bar" pattern="&gt;&quot;it" required="required"',
            $this->newSubject()->renderConstraintsAttributes($field)
        );
    }

    /**
     * @expectedException \Ingenerator\Form\Renderer\UndefinedTemplateException
     */
    public function test_it_throws_if_no_template_defined_for_the_element_class_being_rendered()
    {
        $this->config = FormConfig::withDefaults(
            ['template_map' => [BodyTextFormElement::class => ['edit' => NULL]]]
        );
        $this->newSubject()->render(new BodyTextFormElement(['content' => 'howdy']));
    }

    public function test_it_returns_output_of_simple_template_for_single_field()
    {
        $output = $this->newSubject()->render(
            new TextField(['label' => 'What is your name?', 'name' => 'name'])
        );

        $this->assertContains('What is your name?', $output);
        $this->assertFalse($this->hasOutput(), 'Should not have output anything directly');
    }

    public function test_it_returns_combined_output_of_template_for_field_with_children()
    {
        $group  = new FormGroupElement(
            [
                'type'   => 'group',
                'label'  => 'General',
                'fields' => [
                    ['type' => 'text', 'name' => 'foo', 'label' => 'foo'],
                    ['type' => 'text', 'name' => 'bar', 'label' => 'Barry'],
                ]
            ],
            $this->getElementFactory()
        );
        $output = $this->newSubject()->render($group);
        $this->assertContains('General', $output);
        $this->assertContains('name="foo"', $output);
        $this->assertContains('Barry', $output);
    }

    public function test_it_renders_custom_form_class_with_custom_template_if_mapped()
    {
        $tmp_file     = \tempnam(\sys_get_temp_dir(), 'render-test-php');
        $this->config = FormConfig::withDefaults(
            ['template_map' => [RenderTestForm::class => ['edit' => $tmp_file]]]
        );
        \file_put_contents($tmp_file, 'I am the walrus');
        try {
            $form   = new RenderTestForm(
                ['elements' => [['type' => 'text', 'name' => 'foo', 'label' => 'foo']]],
                $this->getElementFactory()
            );
            $output = $this->newSubject()->render($form);

        } finally {
            \unlink($tmp_file);
        }

        $this->assertSame('I am the walrus', $output);
    }

    public function test_it_renders_custom_form_class_with_default_template_if_not_mapped()
    {
        $this->config = FormConfig::withDefaults();

        $form   = new RenderTestForm(
            [
                'elements' => [
                    ['type' => 'text', 'name' => 'foo', 'label' => 'foo'],
                    ['type' => 'text', 'name' => 'bar', 'label' => 'Barry'],
                ]
            ],
            $this->getElementFactory()
        );
        $output = $this->newSubject()->render($form);

        $this->assertContains('name="foo"', $output);
        $this->assertContains('Barry', $output);
    }

    /**
     * @testWith ["edit", "<input"]
     *           ["display", "form-answer-group"]
     */
    public function test_it_selects_template_based_on_configured_render_mode($mode, $expect_output)
    {
        $this->render_mode = $mode;
        $output            = $this->newSubject()->render(
            new TextField(['label' => 'What is your name?', 'name' => 'name'])
        );

        $this->assertContains($expect_output, $output);
    }

    public function provider_highlighter_classes()
    {
        return [
            [
                ['highlight_if' => ['empty']],
                '',
                'answer-highlighted answer-empty'
            ],
            [
                ['hide_display_if' => ['empty']],
                '',
                'answer-display-hidden answer-empty'
            ],
            [
                [],
                '',
                'answer-empty'
            ],
            [
                ['highlight_if' => ['value:Bob']],
                'Bob',
                'answer-highlighted'
            ],
        ];
    }


    /**
     * @dataProvider provider_highlighter_classes
     */
    public function test_it_can_give_highlight_classes_for_field_criteria(
        $criteria,
        $value,
        $expect
    ) {
        $this->matcher = new FieldCriteriaMatcher;
        $field         = new TextField(
            \array_merge(['label' => 'Name?', 'name' => 'name'], $criteria)
        );
        $this->assertEquals($expect, $this->newSubject()->getHighlightClasses($value, $field));
    }

    public function setUp()
    {
        parent::setUp();
        $this->config = FormConfig::withDefaults();
    }

    protected function newSubject()
    {
        return new FormElementRenderer($this->config, $this->render_mode);
    }

    /**
     * @return \Ingenerator\Form\FormElementFactory
     */
    protected function getElementFactory()
    {
        return new FormElementFactory(FormConfig::withDefaults());
    }

}


class RenderTestForm extends Form
{
}
