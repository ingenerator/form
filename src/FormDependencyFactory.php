<?php
/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */

namespace Ingenerator\Form;


use Ingenerator\Form\Renderer\FormDisplayRenderer;
use Ingenerator\Form\Renderer\FormEditRenderer;
use Ingenerator\Form\Renderer\FormElementRenderer;

class FormDependencyFactory
{
    /**
     * @return array
     */
    public static function definitions()
    {
        return [
            'form' => [
                'config'          => [
                    '_settings' => [
                        'class'       => FormConfig::class,
                        'constructor' => 'withDefaults',
                        'arguments'   => ['@form.form_config@'],
                    ],
                ],
                'element_factory' => [
                    '_settings' => [
                        'class'     => FormElementFactory::class,
                        'arguments' => ['%form.config%']
                    ]
                ],
                'renderer'        => [
                    'edit'    => [
                        '_settings' => [
                            'class'     => FormElementRenderer::class,
                            'arguments' => ['%form.config%', 'edit']
                        ],
                    ],
                    'display' => [
                        '_settings' => [
                            'class'     => FormElementRenderer::class,
                            'arguments' => ['%form.config%', 'display']
                        ],
                    ]
                ],
                'validator'       => [
                    '_settings' => [
                        'class' => FormValidator::class,
                    ],
                ],
            ]
        ];
    }

}
