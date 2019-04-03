<?php
/**
 * @var TextField|TextareaField|ChoiceField|DateField $field
 * @var FormElementRenderer                           $form_renderer
 */

use Ingenerator\Form\Element\Field\TextField;
use Ingenerator\Form\Element\Field\TextareaField;
use Ingenerator\Form\Element\Field\ChoiceField;
use Ingenerator\Form\Element\Field\DateField;
use Ingenerator\Form\Renderer\FormElementRenderer;

$answer_paragraphs = \preg_split('/\n\s*/', $field->display_value ?: 'Left blank');
$classes           = $form_renderer->getHighlightClasses($field->html_value, $field);
?>
<div class="form-answer-group <?= $classes; ?>">
    <label class="form-answer-label">
        <?= $field->display_label; ?>
    </label>
    <?php foreach ($answer_paragraphs as $paragraph): ?>
        <p class="form-answer"><?= HTML::chars($paragraph); ?></p>
    <?php endforeach; ?>
</div>
