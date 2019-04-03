<?php
/**
 * Renders a textarea.
 * @var \Ingenerator\Form\Element\Field\TextareaField $field
 * @var FormElementRenderer                           $form_renderer
 */

use Ingenerator\Form\Renderer\FormElementRenderer;

?>
<div class="form-group <?= $field->errors ? 'has-error' : ''; ?>"
    <?=\HTML::attributes($field->container_data);?>
    >
    <label class="control-label" for="<?= $field->id; ?>"><?= $field->label; ?></label>
    <textarea class="form-control"
              name="<?= $field->name; ?>"
              id="<?= $field->id; ?>"
              placeholder="<?= $field->empty_value; ?>"
              <?php if ($field->rows): ?>
                rows="<?=$field->rows;?>"
              <?php endif; ?>
              <?=$form_renderer->renderConstraintsAttributes($field);?>
    ><?= HTML::chars($field->html_value); ?></textarea>
    <?php if ($field->errors): ?>
        <p class="help-block"><?= \implode(', ', $field->errors); ?></p>
    <?php elseif ($field->help_text): ?>
        <p class="help-block"><?= $field->help_text; ?></p>
    <?php endif; ?>
</div>
