<?php
/**
 * Renders a choice or other field
 * @var array                                $field
 * @var \Ingenerator\Form\Util\FormDataArray $data
 */

use Ingenerator\Form\Criteria\FieldCriteriaMatcher;

$matcher = new FieldCriteriaMatcher();
$values  = $data->getRawValue($field['name']);
$choice  = $values['choice'];
$classes = implode(' ', array_keys(array_filter([
    'answer-highlighted'    => $matcher->matches($choice, \Arr::get($field, 'highlight_if', [])),
    'answer-display-hidden' => $matcher->matches($choice, \Arr::get($field, 'hide_display_if', [])),
    'answer-empty'          => $matcher->matches($choice, ['empty'])
])));
if ( ! $choice) :
    $display_value = \Arr::get($field, 'empty_value', 'Left blank');
elseif (in_array($choice, $field['other_for_values'])) :
    $display_value = $choice . ' - '.$values['detail'];
else:
    $display_value = $choice;
endif;
?>
<div class="form-answer-group <?= $classes; ?>">
    <label class="form-answer-label">
        <?= \Arr::get($field, 'display_label', $field['label']); ?>
    </label>
    <p class="form-answer"><?=HTML::chars($display_value);?></p>
</div>
