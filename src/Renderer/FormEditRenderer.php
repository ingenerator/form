<?php
/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */

namespace Ingenerator\Form\Renderer;

use Ingenerator\Form\Element\AbstractFormElement;


/**
 * Depend on this interface for future-compatibilty of views that render a form in edit mode
 */
interface FormEditRenderer
{

    /**
     * @param AbstractFormElement $element
     *
     * @return string
     */
    public function render(AbstractFormElement $element);
}
