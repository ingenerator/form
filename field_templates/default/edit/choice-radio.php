<?php
/**
 * Renders a choice field as a select
 * @var \Ingenerator\Form\Element\Field\ChoiceField $field
 * @var FormElementRenderer                         $form_renderer
 */

use Ingenerator\Form\Renderer\FormElementRenderer;

?>
<div class="form-group <?= $field->errors ? 'has-error' : ''; ?>"
    <?= \HTML::attributes($field->container_data); ?>
>
    <label class="control-label"><?= $field->label; ?></label>
    <div class="<?= $field->length ? "form-control-{$field->length}-text" : ""; ?>">
        <?php foreach ($field->choices as $choice): ?>
            <div class="radio">
                <label>
                    <input type="radio"
                           name="<?= $field->name; ?>"
                           value="<?= $choice['value']; ?>"
                        <?= $choice['selected']; ?>
                        <?= $choice['disabled']; ?>
                        <?= $form_renderer->renderConstraintsAttributes($field); ?>
                    >
                    <?= $choice['caption']; ?>
                </label>
            </div>
        <?php endforeach; ?>
    </div>
    <?php if ($field->errors): ?>
        <p class="help-block"><?= implode(', ', $field->errors); ?></p>
    <?php elseif ($field->help_text): ?>
        <p class="help-block"><?= $field->help_text; ?></p>
    <?php endif; ?>
</div>
