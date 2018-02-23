<?php
/**
 * Renders a choice field as a select
 *
 * @var ChoiceOrOtherField $field
 * @var FormElementRenderer $form_renderer
 */

use Ingenerator\Form\Element\Field\ChoiceOrOtherField;
use Ingenerator\Form\Renderer\FormElementRenderer;
use View\Helper\ShowgroupHelper;

$length_class = $field->length ? 'form-control-'.$field->length.'-text' : '';
$showgroup    = new ShowgroupHelper($field->choice_field->html_value);
?>
<div class="form-group"
     data-showgroup-container
     <?=\HTML::attributes($field->container_data);?>
    >
    <label class="control-label"
           for="<?= $field->choice_field->id; ?>"><?= $field->label; ?></label>
    <div class="row">
        <div class="col-sm-6 <?= $length_class; ?>">
            <select class="form-control <?= $length_class; ?>"
                    name="<?= $field->choice_field->name; ?>"
                    id="<?= $field->choice_field->id; ?>"
                    data-showgroup-toggle
            >
                <?php foreach ($field->choice_field->choices as $choice): ?>
                    <option value="<?= $choice['value']; ?>"
                        <?= $choice['selected']; ?>
                        <?= $choice['disabled']; ?>><?= $choice['caption']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-sm-6 <?= $length_class; ?>"
            <?= $showgroup->attrsForGroup($field->other_for_values); ?>
        ><label class="sr-only"
                for="<?= $field->detail_field->id; ?>"><?= $field->detail_field->label; ?></label>
            <input id="<?= $field->detail_field->id; ?>"
                   class="form-control <?= $length_class; ?>"
                   name="<?= $field->detail_field->name; ?>"
                   value="<?= HTML::chars($field->detail_field->html_value); ?>"
                   placeholder="Please state"
            >
        </div>
    </div>
</div>
