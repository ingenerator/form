<?php
/**
 * A simple group of fields as fieldset
 * @var \Ingenerator\Form\Element\FormGroupElement $field
 * @var FormElementRenderer                        $form_renderer
 */

use Ingenerator\Form\Renderer\FormElementRenderer;

?>
<fieldset <?=\HTML::attributes($field->container_data);?>>
    <legend><?= HTML::chars($field->label); ?></legend>
    <?php foreach ($field->fields as $child_field): ?>
        <?= $form_renderer->render($child_field); ?>
    <?php endforeach; ?>
</fieldset>
