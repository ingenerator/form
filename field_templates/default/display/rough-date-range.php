<?php
/**
 * Renders a pair of text fields on one row for a "date from", "date to" type arrangement
 * @var array         $field
 * @var FormDataArray $data
 */

use Ingenerator\Form\Criteria\FieldCriteriaMatcher;
use Ingenerator\Form\Util\FormDataArray;

$matcher = new FieldCriteriaMatcher();
$value = $data->getRawValue($field['name']);
$classes = implode(' ', array_keys(array_filter([
    'answer-highlighted'    => $matcher->matches($value, \Arr::get($field, 'highlight_if', [])),
    'answer-display-hidden' => $matcher->matches($value, \Arr::get($field, 'hide_display_if', [])),
])));
$text_value = implode(' - ', $value ? : array());
?>
<div class="form-answer-group <?=$classes;?>">
    <label class="form-answer-label">
        Date From - To
    </label>
    <p class="form-answer">
        <?php if ($text_value): ?>
            <?=HTML::chars($text_value);?>
        <?php else: ?>
            <?=\Arr::get($field, 'empty_value', 'Left blank');?>
        <?php endif; ?>
    </p>
</div>
