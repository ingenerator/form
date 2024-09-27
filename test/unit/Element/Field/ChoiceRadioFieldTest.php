<?php
/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */

namespace test\unit\Ingenerator\Form\Element\Field;


use Ingenerator\Form\Element\Field\ChoiceRadioField;
use function array_merge;

class ChoiceRadioFieldTest extends ChoiceFieldTest
{

    public static function provider_valid_options_and_defaults(): array
    {
        $defaults                     = parent::provider_valid_options_and_defaults();
        $defaults['add_empty_choice'] = ['add_empty_choice', FALSE, TRUE];
        $defaults['bordered_choices'] = ['bordered_choices', FALSE, TRUE];

        return $defaults;
    }

    public static function provider_auto_empty_choice(): array
    {
        $auto_empty = [
            'value'    => '',
            'caption'  => NULL,
            'selected' => 'selected',
            'disabled' => 'disabled'
        ];
        $one_one    = ['value' => '1', 'caption' => 'One'];

        return [
            [
                // When configured and with no empty choice in list, prepends auto-empty
                ['add_empty_choice' => TRUE, 'choices' => ['One']],
                [
                    $auto_empty,
                    ['value' => 'One', 'caption' => 'One', 'selected' => '', 'disabled' => ''],
                ]
            ],
            [
                // When configured and with no empty choice in list, prepends auto-empty with custom text
                [
                    'add_empty_choice' => TRUE,
                    'choices'          => ['One'],
                    'empty_value'      => 'Go on, select'
                ],
                [
                    array_merge($auto_empty, ['caption' => 'Go on, select']),
                    ['value' => 'One', 'caption' => 'One', 'selected' => '', 'disabled' => ''],
                ]
            ],
            [
                // Explicit empty choice in list is selectable and prevents prepending auto even if set
                ['add_empty_choice' => TRUE, 'choices' => ['One', '']],
                [
                    ['value' => 'One', 'caption' => 'One', 'selected' => '', 'disabled' => ''],
                    ['value' => '', 'caption' => '', 'selected' => 'selected', 'disabled' => ''],
                ]
            ],
            [
                // Auto-empty choice can be disabled
                ['add_empty_choice' => FALSE, 'choices' => ['One']],
                [
                    ['value' => 'One', 'caption' => 'One', 'selected' => '', 'disabled' => ''],
                ]
            ],
            [
                // Auto-empty choice disabled by default
                ['choices' => ['One']],
                [
                    ['value' => 'One', 'caption' => 'One', 'selected' => '', 'disabled' => ''],
                ]
            ],
            [
                // When configured and with no empty choice in list, prepends auto-empty
                ['add_empty_choice' => TRUE, 'choices' => [$one_one]],
                [
                    $auto_empty,
                    ['value' => '1', 'caption' => 'One', 'selected' => '', 'disabled' => ''],
                ]
            ],
            [
                // When configured and with no empty choice in list, prepends auto-empty with custom text
                ['add_empty_choice' => TRUE, 'choices' => [$one_one], 'empty_value' => 'Do it'],
                [
                    array_merge($auto_empty, ['caption' => 'Do it']),
                    ['value' => '1', 'caption' => 'One', 'selected' => '', 'disabled' => ''],
                ]
            ],
            [
                // Explicit empty choice in list is selectable and prevents prepending auto
                [
                    'add_empty_choice' => TRUE,
                    'choices'          => [$one_one, ['value' => '', 'caption' => '']]
                ],
                [
                    ['value' => '1', 'caption' => 'One', 'selected' => '', 'disabled' => ''],
                    ['value' => '', 'caption' => '', 'selected' => 'selected', 'disabled' => ''],
                ]
            ],
            [
                // Auto-empty choice is disabled by default
                ['choices' => [$one_one]],
                [
                    ['value' => '1', 'caption' => 'One', 'selected' => '', 'disabled' => ''],
                ]
            ],

        ];
    }

    protected function newSubject(array $values = []): ChoiceRadioField
    {
        $default = [
            'name'    => 'foofield',
            'label'   => 'What\'s the best foo?',
            'choices' => ['One']
        ];

        return new ChoiceRadioField(array_merge($default, $values));
    }


}
