<?php
/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */
namespace Ingenerator\Form\Element;

/**
 * @property-read string  content
 * @property-read boolean hide_display
 */
class BodyTextFormElement extends AbstractFormElement
{

    protected function getDefaultSchema()
    {
        return [
            'content'      => NULL,
            'hide_display' => FALSE
        ];
    }

    protected function listRequiredSchemaKeys()
    {
        return ['content'];
    }
}
