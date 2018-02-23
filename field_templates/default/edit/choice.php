<?php
/**
 * Renders a choice field as a select
 * @var \Ingenerator\Form\Element\Field\ChoiceField $field
 * @var FormElementRenderer                         $form_renderer
 */

use Ingenerator\Form\Renderer\FormElementRenderer;

?>
<div class="form-group <?= $field->errors ? 'has-error' : ''; ?>"
    <?=\HTML::attributes($field->container_data);?>
    >
    <label class="control-label" for="<?= $field->id; ?>"><?= $field->label; ?></label>
    <select class="form-control <?= $field->length ? "form-control-{$field->length}-text" : ""; ?>"
            id="<?= $field->id; ?>"
            name="<?= $field->name; ?>"
            <?=$form_renderer->renderConstraintsAttributes($field);?>
    >
        <?php foreach ($field->choices as $choice): ?>
            <option value="<?= $choice['value']; ?>"
                <?= $choice['selected']; ?>
                <?= $choice['disabled']; ?>><?= $choice['caption']; ?></option>
        <?php endforeach; ?>
    </select>
    <?php if ($field->errors): ?>
        <p class="help-block"><?= implode(', ', $field->errors); ?></p>
    <?php elseif ($field->help_text): ?>
        <p class="help-block"><?= $field->help_text; ?></p>
    <?php endif; ?>
</div>
