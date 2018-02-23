<?php
/**
 * Renders a choice or other field
 * @var \Ingenerator\Form\Element\Field\ChoiceOrOtherField $field
 * @var \Ingenerator\Form\Renderer\FormElementRenderer     $form_renderer
 */

$classes = $form_renderer->getHighlightClasses($field->choice_field->html_value, $field);
?>
<div class="form-answer-group <?= $classes; ?>">
    <label class="form-answer-label">
        <?= $field->label; ?>
    </label>
    <p class="form-answer"><?= HTML::chars($field->display_value ?: 'Left blank'); ?></p>
</div>
