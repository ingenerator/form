<?php
/**
 * @var ChoiceRadioField    $field
 * @var FormElementRenderer $form_renderer
 */

use Ingenerator\Form\Element\Field\ChoiceRadioField;
use Ingenerator\Form\Renderer\FormElementRenderer;

$classes = $form_renderer->getHighlightClasses($field->html_value, $field);
?>
<div class="form-answer-group <?=$classes;?>">
    <label class="form-answer-label">
        <?=$field->display_label;?>
    </label>
    <p class="form-answer">
        <?php
        // NOTE: No need to html-escape the value as it comes from the schema definition and may validly contain
        // markup (the value is not html-escaped in the edit template).
        ?>
        <?=$field->display_value ?: 'Left blank';?>
    </p>
</div>
