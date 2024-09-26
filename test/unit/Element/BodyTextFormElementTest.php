<?php
/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */

namespace test\unit\Ingenerator\Form\Element;


use Ingenerator\Form\Element\AbstractFormElement;
use Ingenerator\Form\Element\BodyTextFormElement;
use Ingenerator\Form\TestSupport\PHPUnit10\BaseFormElementTestCase;
use function array_merge;

class BodyTextFormElementTest extends BaseFormElementTestCase
{
    public static function provider_required_options(): array
    {
        return [
            ['content']
        ];
    }

    public static function provider_valid_options_and_defaults(): array
    {
        $options   = parent::provider_valid_options_and_defaults();
        $options[] = ['hide_display', FALSE, TRUE];

        return $options;
    }

    protected function newSubject(array $values = []): AbstractFormElement
    {
        $default = [
            'content' => '<h2>Here is some html</h2>',
        ];

        return new BodyTextFormElement(array_merge($default, $values));
    }


}

