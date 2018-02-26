<?php
/**
 * Renders a nested repeating-group field that allows the user to clone and add multiple
 * sets with the same fields
 *
 * @var RepeatingGroupField $field
 * @var FormElementRenderer $form_renderer
 */

use Ingenerator\Form\Element\Field\RepeatingGroupField;
use Ingenerator\Form\Renderer\FormElementRenderer;

?>
<h4 class="control-label"><?= $field->label; ?></h4>
<div class="form-repeating-group-container" data-cloneya-container>
    <?php foreach ($field->groups as $field_group): ?>
        <div class="form-repeating-group" data-cloneya-item>
            <div class="form-repeating-remove">
                <a class="btn btn-danger"
                   data-cloneya-action="delete"
                   title="Remove this entry"
                ><i class="fa fa-trash fa-lg"></i><span class="sr-only">Remove</span></a>
            </div>
            <?php foreach ($field_group as $field): ?>
                <?=$form_renderer->render($field);?>
            <?php endforeach; ?>
        </div>
    <?php endforeach; ?>
    <div class="form-repeating-add">
        <a class="btn btn-info btn-block"
           data-cloneya-action="clone-last"
        ><i class="fa fa-plus"></i> Add another</a>
    </div>
</div>
