<?php
/**
 * Renders a grouped choice field as a select with optgroups
 * @var \Ingenerator\Form\Element\Field\GroupedChoiceField $field
 * @var FormElementRenderer                                $form_renderer
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
        <?= $form_renderer->renderConstraintsAttributes($field); ?>
    >
        <?php if ($field->add_empty_choice): ?>
            <option value=""
                <?= $field->is_empty_selected ? 'selected' : ''; ?>
                    disabled><?= $field->empty_value; ?></option>
        <?php endif; ?>
        <?php foreach ($field->choice_groups as $group): ?>
            <optgroup label="<?= $group['group_caption']; ?>">
                <?php foreach ($group['choices'] as $choice): ?>
                    <option value="<?= $choice['value']; ?>"
                        <?= $choice['selected']; ?>
                        <?=\HTML::attributes($choice['data']);?>
                    ><?= $choice['caption']; ?></option>
                <?php endforeach; ?>
            </optgroup>
        <?php endforeach; ?>
    </select>
    <?php if ($field->errors): ?>
        <p class="help-block"><?= implode(', ', $field->errors); ?></p>
    <?php elseif ($field->help_text): ?>
        <p class="help-block"><?= $field->help_text; ?></p>
    <?php endif; ?>
</div>
