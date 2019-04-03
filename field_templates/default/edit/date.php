<?php
/**
 * Renders a date field as an html5 date input
 *
 * @var \Ingenerator\Form\Element\Field\DateField $field
 * @var FormElementRenderer $form_renderer
 */

use Ingenerator\Form\Renderer\FormElementRenderer;

?>
<div class="form-group <?= $field->errors ? 'has-error' : ''; ?>"
    <?=\HTML::attributes($field->container_data);?>
    >
    <label class="control-label" for="<?= $field->id; ?>"><?= $field->label; ?></label>
    <input class="form-control form-control-short-text"
           type="date"
           name="<?= $field->name; ?>"
           id="<?= $field->id; ?>"
           placeholder="<?= $field->empty_value; ?>"
           value="<?= HTML::chars($field->html_value); ?>"
           <?=$form_renderer->renderConstraintsAttributes($field);?>
    >
    <?php if ($field->errors): ?>
        <p class="help-block"><?= \implode(', ', $field->errors); ?></p>
    <?php elseif ($field->help_text): ?>
        <p class="help-block"><?= $field->help_text; ?></p>
    <?php endif; ?>
</div>
