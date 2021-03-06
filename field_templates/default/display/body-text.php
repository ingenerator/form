<?php
/**
 * Renders a body-text element from a form
 * @var BodyTextFormElement $field
 * @var FormElementRenderer $form_renderer
 */

use Ingenerator\Form\Element\BodyTextFormElement;
use Ingenerator\Form\Renderer\FormElementRenderer;

// NB this intentionally doesn't render the content at all (unlike the fields where it's rendered
// with an invisible CSS class. This is because the HTML might include images etc that will break
// PDF rendering of a form or document.
?>
<?php if ( ! $field->hide_display): ?>
    <?= $field->content; ?>
<?php endif; ?>
