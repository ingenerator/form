<?php
/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */

namespace test\unit\Ingenerator\Form\Element;


use Ingenerator\Form\Element\BodyTextFormElement;

class BodyTextFormElementTest extends BaseFormElementTest
{
    public function provider_required_options()
    {
        return [
            ['content']
        ];
    }

    public function provider_valid_options_and_defaults()
    {
        $options   = parent::provider_valid_options_and_defaults();
        $options[] = ['hide_display', FALSE, TRUE];

        return $options;
    }

    /**
     * @param array $values
     *
     * @return \Ingenerator\Form\Element\BodyTextFormElement
     */
    protected function newSubject(array $values = [])
    {
        $default = [
            'content' => '<h2>Here is some html</h2>',
        ];

        return new BodyTextFormElement(\array_merge($default, $values));
    }


}

