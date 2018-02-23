<?php
/**
 * Renders a nested repeating-group field
 * sets with the same fields
 * @var array         $field
 * @var FormDataArray $data
 */

use Ingenerator\Form\Util\FormDataArray;

$groups = $data->getGroupIndices($field['name']);
?>
<div class="form-answer-group <?=$groups ? '' : 'answer-empty';?>">
    <label class="form-answer-label">
        <?= \Arr::get($field, 'display_label', $field['label']); ?>
    </label>
    <?php if ($groups): ?>
        <?php foreach ($groups as $group_index): ?>
            <div class="form-repeating-answer">
                <?php foreach ($field['fields'] as $subfield):
                    $subfield['name'] = sprintf('%s[%s]%s', $field['name'], $group_index,
                        $subfield['name']);
                    ?>
                    <?= View::factory('form_fields/display/'.$subfield['type'],
                    ['field' => $subfield, 'data' => $data])->render(); ?>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="form-answer">
            <?=\Arr::get($field, 'empty_value', 'None entered');?>
        </p>
    <?php endif; ?>
</div>
