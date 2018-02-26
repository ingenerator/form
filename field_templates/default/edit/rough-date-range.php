<?php
/**
 * Renders a pair of text fields on one row for a "date from", "date to" type arrangement
 *
 * @var \Ingenerator\Form\Element\Field\RoughDateRangeField $field
 * @var FormElementRenderer                                 $form_renderer
 */

use Ingenerator\Form\Renderer\FormElementRenderer;

?>
<div class="row" <?=\HTML::attributes($field->container_data);?>>
    <div class="col-sm-3">
        <?= $form_renderer->render($field->from_field); ?>
    </div>
    <div class="col-sm-3">
        <?= $form_renderer->render($field->to_field); ?>
    </div>
</div>
