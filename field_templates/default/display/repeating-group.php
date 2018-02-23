<?php
/**
 * Renders a nested repeating-group field sets with the same fields
 * @var RepeatingGroupField $field
 * @var FormElementRenderer $form_renderer
 */

use Ingenerator\Form\Element\Field\RepeatingGroupField;
use Ingenerator\Form\Renderer\FormElementRenderer;

?>
<div class="form-answer-group <?= $field->groups ? '' : 'answer-empty'; ?>">
    <label class="form-answer-label">
        <?= $field->display_label; ?>
    </label>
    <?php if ($field->groups): ?>
        <?php foreach ($field->groups as $group_fields): ?>
            <div class="form-repeating-answer">
                <?php foreach ($group_fields as $child_field): ?>
                    <?= $form_renderer->render($child_field); ?>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="form-answer">
            <?= $field->empty_value ?: 'None entered'; ?>
        </p>
    <?php endif; ?>
</div>
