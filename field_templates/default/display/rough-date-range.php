<?php
/**
 * Renders a pair of text fields on one row for a "date from", "date to" type arrangement
 * @var RoughDateRangeField $field
 * @var FormElementRenderer $form_renderer
 */

use Ingenerator\Form\Element\Field\RoughDateRangeField;
use Ingenerator\Form\Renderer\FormElementRenderer;

$classes = $form_renderer->getHighlightClasses($field->display_value, $field);
?>
<div class="form-answer-group <?= $classes; ?>">
    <label class="form-answer-label">
        Date From - To
    </label>
    <p class="form-answer">
        <?= HTML::chars($field->display_value ?: 'Left blank'); ?>
    </p>
</div>
