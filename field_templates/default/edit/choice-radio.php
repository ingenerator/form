<?php
/**
 * Renders a choice field as a list of radio buttons
 * @var \Ingenerator\Form\Element\Field\ChoiceRadioField $field
 * @var FormElementRenderer                         $form_renderer
 */

use Ingenerator\Form\Renderer\FormElementRenderer;

$classes = array_filter(
    [
        $field->length ? "form-control-{$field->length}-text" : "",
        $field->bordered_choices ? 'choice-list--bordered' : '',
    ]
);

?>
<div class="form-group <?= $field->errors ? 'has-error' : ''; ?>"
    <?= \HTML::attributes($field->container_data); ?>
>
    <label class="control-label"><?= $field->label; ?></label>
    <div class="<?=implode(' ', $classes); ?>"
         data-choice-radio-name="<?=$field->name;?>"
    >
        <?php foreach ($field->choices as $choice): ?>
            <div class="radio">
                <label>
                    <input type="radio"
                           name="<?= $field->name; ?>"
                           value="<?= $choice['value']; ?>"
                        <?= $choice['selected'] ? 'checked' : ''; ?>
                        <?= $choice['disabled']; ?>
                        <?= $form_renderer->renderConstraintsAttributes($field); ?>
                    >
                    <?= $choice['caption']; ?>
                </label>
            </div>
        <?php endforeach; ?>
    </div>
    <?php if ($field->errors): ?>
        <p class="help-block"><?= \implode(', ', $field->errors); ?></p>
    <?php elseif ($field->help_text): ?>
        <p class="help-block"><?= $field->help_text; ?></p>
    <?php endif; ?>
</div>
<script>
    (function () {
        if (!(document.createElement('div').classList && NodeList.prototype.forEach)) {
            // progressively enhance for modern browsers, no point polyfilling
            return;
        }
        const container = document.querySelector('[data-choice-radio-name="' + <?=json_encode($field->name);?> + '"]');
        const choices = Array.from(container.querySelectorAll('.radio'))
            .map((div) => ({div, radio: div.querySelector('input[type=radio]')}));

        function highlightSelected() {
            choices.forEach(({div, radio}) => div.classList.toggle('choice--selected', radio.checked));
        }

        container.addEventListener('change', highlightSelected)
        highlightSelected();
    })();
</script>

