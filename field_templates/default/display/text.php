<?php
/**
 * Renders a text field - optionally specifying the type of field to render
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
    'answer-empty'          => $matcher->matches($value, ['empty'])
])));
$display_value = $value ?: \Arr::get($field, 'empty_value', 'Left blank');
$answer_paragraphs = preg_split('/\n\s*/', $display_value);
?>
<div class="form-answer-group <?=$classes;?>">
    <label class="form-answer-label">
        <?= \Arr::get($field, 'display_label', $field['label']); ?>
    </label>
    <?php foreach ($answer_paragraphs as $paragraph): ?>
        <p class="form-answer"><?=HTML::chars($paragraph);?></p>
    <?php endforeach;?>
</div>
