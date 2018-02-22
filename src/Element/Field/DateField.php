<?php
/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */

namespace Ingenerator\Form\Element\Field;

use Ingenerator\Form\Util\FormDataArray;
use Ingenerator\PHPUtils\DateTime\DateTimeImmutableFactory;


class DateField extends AbstractFormField
{

    public function validateConstraintSchema(array $constraints)
    {
        // Temporarily accept anything
    }

    /**
     * Initialises a value from either a model (expects \DateTimeImmutable) or an incoming
     * HTTP POST string
     *
     * @param \Ingenerator\Form\Util\FormDataArray $post
     */
    public function assignValue(FormDataArray $post)
    {
        $value = $post->getRawValue($this->name);
        if ($value instanceof \DateTimeImmutable) {
            $this->html_value = $value->format('Y-m-d');
        } else {
            $this->html_value = (string) $value;
        }
    }

    /**
     * Captures the field value as either NULL, an InvalidUserDateTime or a DateTimeImmutable
     *
     * @param \Ingenerator\Form\Util\FormDataArray $data
     */
    public function collectValue(FormDataArray $data)
    {
        // Parses an invalid input to an InvalidUserDateTime as this is required for validation to
        // mark the field invalid rather than empty because of internal kohana logic around `FALSE`
        $value = $this->html_value
            ? DateTimeImmutableFactory::fromYmdInput($this->html_value)
            : NULL;
        $data->setFieldValue($this->name, $value);
    }

}
