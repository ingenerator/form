<?php
/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */

namespace Ingenerator\Form\Renderer;

use Ingenerator\Form\Element\AbstractFormElement;


/**
 * Depend on this interface for future-compatibility of views that render a form in display mode.
 *
 */
interface FormDisplayRenderer
{
    /**
     * @param AbstractFormElement $element
     *
     * @return string
     */
    public function render(AbstractFormElement $element);
}
