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
use Ingenerator\Form\InvalidFormConfigException;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;
use stdClass;
use function realpath;

class FormConfigTest extends TestCase
{
    protected array $config = [
        'element_type_map' => [],
        'template_map'     => []
    ];

    public function test_it_is_initialisable(): void
    {
        $this->assertInstanceOf(FormConfig::class, $this->newSubject());
    }

    public function test_it_throws_without_element_type_map(): void
    {
        unset($this->config['element_type_map']);
        $this->expectException(InvalidFormConfigException::class);
        $this->newSubject();
    }

    public function test_it_throws_without_template_map(): void
    {
        unset($this->config['template_map']);
        $this->expectException(InvalidFormConfigException::class);
        $this->newSubject();
    }

    #[TestWith(['text', 'My\TextFieldClass'])]
    #[TestWith(['choice', 'My\ChoiceFieldClass'])]
    #[TestWith(['rubbish', NULL])]
    public function test_it_provides_element_type_or_null($type, $expect): void
    {
        $this->config['element_type_map'] = [
            'text'   => 'My\TextFieldClass',
            'choice' => 'My\ChoiceFieldClass'
        ];
        $subject                          = $this->newSubject();
        $this->assertSame($expect, $subject->getElementClass($type));
    }

    public function test_it_lists_defined_types(): void
    {
        $this->config['element_type_map'] = [
            'text'   => 'My\TextFieldClass',
            'choice' => 'My\ChoiceFieldClass',
            'crazy'  => NULL,
        ];
        $this->assertEquals(['text', 'choice'], $this->newSubject()->listDefinedElementTypes());
    }

    #[TestWith(['My\TextFieldClass', 'edit', '/path/to/edit/text.php'])]
    #[TestWith(['My\ChoiceFieldClass', 'edit', '/path/to/edit/choice.php'])]
    #[TestWith(['My\TextFieldClass', 'display', '/path/to/display/text.php'])]
    #[TestWith(['My\ChoiceFieldClass', 'display', NULL])]
    #[TestWith(['My\RandomClass', 'edit', NULL])]
    public function test_it_provides_template_file_or_null($class, $mode, $expect): void
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

    public function test_with_default_constructor_provides_standard_element_type(): void
    {
        $subject = FormConfig::withDefaults();
        $this->assertSame(TextField::class, $subject->getElementClass('text'));
        $this->assertSame(FormGroupElement::class, $subject->getElementClass('group'));
    }

    public function test_with_default_constructor_provides_standard_template(): void
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


    #[TestWith(['shoe-size', 'My\ShoesizeField'])]
    #[TestWith(['text', 'My\TextField'])]
    #[TestWith(['choice', NULL])]
    public function test_with_default_constructor_can_override_element_type($type, $expect): void
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

    #[TestWith(['My\TextFieldClass', 'edit', '/path/to/edit/text.php'])]
    #[TestWith(['My\TextFieldClass', 'display', '/path/to/display/text.php'])]
    #[TestWith(['Ingenerator\Form\Element\Field\TextField', 'edit', '/custom/edit/text.php'])]
    #[TestWith(['Ingenerator\Form\Element\Field\TextField', 'display', NULL])]
    public function test_with_default_constructor_can_override_template($class, $mode, $expect): void
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

    public function test_is_valid_with_default_config(): void
    {
        FormConfig::withDefaults()->validate();
        // Validate returns without throwing
        $this->addToAssertionCount(1);
    }

    public function test_is_valid_when_custom_config_is_valid(): void
    {
        FormConfig::withDefaults(
                [
                    'element_type_map' => [
                        'stdclass' => stdClass::class,
                    ],
                    'template_map'     => [
                        stdClass::class => [
                            'edit' => __FILE__
                        ]
                    ]
                ]
        )->validate();
        // Validate returns without throwing
        $this->addToAssertionCount(1);
    }

    #[TestWith([['element_type_map' => ['junk' => 'some\junk\field']]])]
    #[TestWith([['template_map' => ['\junk\field' => ['edit' => '/no/file/here.php']]]])]
    public function test_validate_throws_when_missing_files_or_classes($invalid_config)
    {
        $subject = FormConfig::withDefaults($invalid_config);
        $this->expectException(InvalidFormConfigException::class);
        $subject->validate();
    }

    protected function newSubject(): FormConfig
    {
        return new FormConfig($this->config);
    }

}
