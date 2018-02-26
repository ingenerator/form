<?php
/**
 * A form - by default no markup required other than the fields themselves
 * @var \Ingenerator\Form\Form $field
 * @var FormElementRenderer    $form_renderer
 */

use Ingenerator\Form\Renderer\FormElementRenderer;

?>
<?php foreach ($field->elements as $element): ?>
    <?= $form_renderer->render($element); ?>
<?php endforeach; ?>
