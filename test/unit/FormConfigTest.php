<?php
/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */

namespace test\unit\Ingenerator\Form;


use Ingenerator\Form\Element\Field\DateField;
use Ingenerator\Form\Element\Field\RoughDateRangeField;
use Ingenerator\Form\Element\Field\TextField;
use Ingenerator\Form\Element\FormGroupElement;
use Ingenerator\Form\FormConfig;

class FormConfigTest extends \PHPUnit\Framework\TestCase
{
    protected $config = [
        'element_type_map' => [],
        'template_map'     => []
    ];

    public function test_it_is_initialisable()
    {
        $this->assertInstanceOf(FormConfig::class, $this->newSubject());
    }

    /**
     * @expectedException \Ingenerator\Form\InvalidFormConfigException
     */
    public function test_it_throws_without_element_type_map()
    {
        unset($this->config['element_type_map']);
        $this->newSubject();
    }

    /**
     * @expectedException \Ingenerator\Form\InvalidFormConfigException
     */
    public function test_it_throws_without_template_map()
    {
        unset($this->config['template_map']);
        $this->newSubject();
    }

    /**
     * @testWith ["text", "My\\TextFieldClass"]
     *           ["choice", "My\\ChoiceFieldClass"]
     *           ["rubbish", null]
     */
    public function test_it_provides_element_type_or_null($type, $expect)
    {
        $this->config['element_type_map'] = [
            'text'   => 'My\TextFieldClass',
            'choice' => 'My\ChoiceFieldClass'
        ];
        $subject                          = $this->newSubject();
        $this->assertSame($expect, $subject->getElementClass($type));
    }

    public function test_it_lists_defined_types()
    {
        $this->config['element_type_map'] = [
            'text'   => 'My\TextFieldClass',
            'choice' => 'My\ChoiceFieldClass',
            'crazy'  => NULL,
        ];
        $this->assertEquals(['text', 'choice'], $this->newSubject()->listDefinedElementTypes());
    }

    /**
     * @testWith ["My\\TextFieldClass", "edit", "/path/to/edit/text.php"]
     *           ["My\\ChoiceFieldClass", "edit", "/path/to/edit/choice.php"]
     *           ["My\\TextFieldClass", "display", "/path/to/display/text.php"]
     *           ["My\\ChoiceFieldClass", "display", null]
     *           ["My\\RandomClass", "edit", null]
     */
    public function test_it_provides_template_file_or_null($class, $mode, $expect)
    {
        $this->config['template_map'] = [
            'My\TextFieldClass'   => [
                'edit'    => '/path/to/edit/text.php',
                'display' => '/path/to/display/text.php'
            ],
            'My\ChoiceFieldClass' => [
                'edit' => '/path/to/edit/choice.php'
            ]
        ];

        $subject = $this->newSubject();

        $this->assertSame($expect, $subject->getTemplateFile($class, $mode));
    }

    public function test_with_default_constructor_provides_standard_element_type()
    {
        $subject = FormConfig::withDefaults();
        $this->assertSame(TextField::class, $subject->getElementClass('text'));
        $this->assertSame(FormGroupElement::class, $subject->getElementClass('group'));
    }

    public function test_with_default_constructor_provides_standard_template()
    {
        $subject = FormConfig::withDefaults();
        $tpl_dir = realpath(__DIR__.'/../../field_templates/default');

        $this->assertSame(
            $tpl_dir.'/edit/date.php',
            $subject->getTemplateFile(DateField::class, 'edit')
        );
        $this->assertSame(
            $tpl_dir.'/display/rough-date-range.php',
            $subject->getTemplateFile(RoughDateRangeField::class, 'display')
        );
    }

    /**
     * @testWith ["shoe-size", "My\\ShoesizeField"]
     *           ["text", "My\\TextField"]
     *           ["choice", null]
     *
     */
    public function test_with_default_constructor_can_override_element_type($type, $expect)
    {
        $subject = FormConfig::withDefaults(
            [
                'element_type_map' => [
                    'shoe-size' => 'My\ShoesizeField',
                    'text'      => 'My\TextField',
                    'choice'    => NULL
                ]
            ]
        );
        $this->assertSame($expect, $subject->getElementClass($type));
    }

    /**
     * @testWith ["My\\TextFieldClass", "edit", "/path/to/edit/text.php"]
     *           ["My\\TextFieldClass", "display", "/path/to/display/text.php"]
     *           ["Ingenerator\\Form\\Element\\Field\\TextField", "edit", "/custom/edit/text.php"]
     *           ["Ingenerator\\Form\\Element\\Field\\TextField", "display", null]
     */
    public function test_with_default_constructor_can_override_template($class, $mode, $expect)
    {
        $subject = FormConfig::withDefaults(
            [
                'template_map' => [
                    'My\TextFieldClass' => [
                        'edit'    => '/path/to/edit/text.php',
                        'display' => '/path/to/display/text.php'
                    ],
                    TextField::class    => [
                        'edit'    => '/custom/edit/text.php',
                        'display' => NULL
                    ]
                ]
            ]
        );
        $this->assertSame($expect, $subject->getTemplateFile($class, $mode));
    }

    public function test_is_valid_with_default_config()
    {
        $this->assertNull(
            FormConfig::withDefaults()->validate(),
            'Validate returns null without throwing'
        );
    }

    public function test_is_valid_when_custom_config_is_valid()
    {
        $this->assertNull(
            FormConfig::withDefaults(
                [
                    'element_type_map' => [
                        'stdclass' => \stdClass::class
                    ],
                    'template_map'     => [
                        \stdClass::class => [
                            'edit' => __FILE__
                        ]
                    ]
                ]
            )->validate(),
            'Validate returns null without throwing'
        );
    }

    /**
     * @testWith [{"element_type_map": {"junk": "some\\junk\\field"}}]
     *           [{"template_map": {"\\junk\\field": {"edit": "/no/file/here.php"}}}]
     *
     * @expectedException \Ingenerator\Form\InvalidFormConfigException
     */
    public function test_validate_throws_when_missing_files_or_classes($invalid_config)
    {
        $subject = FormConfig::withDefaults($invalid_config);
        $subject->validate();
    }

    protected function newSubject()
    {
        return new FormConfig($this->config);
    }

}
