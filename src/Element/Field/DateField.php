<?php
/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */

namespace Ingenerator\Form\Element\Field;

use DateTimeImmutable;
use Ingenerator\Form\Util\FormDataArray;
use Ingenerator\PHPUtils\DateTime\DateTimeImmutableFactory;
use InvalidArgumentException;


class DateField extends AbstractFormField
{

    public function validateConstraintSchema(array $constraints)
    {
        // Temporarily accept anything
    }

    /**
     * Initialises a value from either a model (expects DateTimeImmutable) or an incoming
     * HTTP POST string
     *
     * @param \Ingenerator\Form\Util\FormDataArray $post
     */
    public function assignValue(FormDataArray $post)
    {
        $value = $post->getRawValue($this->name);
        if ($value instanceof DateTimeImmutable) {
            $this->html_value = $value->format('Y-m-d');
        } else {
            $this->html_value = (string) $value;
        }
    }

    /**
     * Captures the field value as either NULL or a string.
     *
     * The string is assumed to be in YYYY-MM-DD format - and will be if returned by a valid HTTP POST -
     * however validating this is a caller concern.
     *
     * @param \Ingenerator\Form\Util\FormDataArray $data
     */
    public function collectValue(FormDataArray $data)
    {
        $data->setFieldValue($this->name, $this->html_value === '' ? null : $this->html_value);
    }

    public function __get($option)
    {
        if ($option === 'display_value') {
            return $this->getDisplayValue();
        }

        return parent::__get($option);
    }

    protected function getDisplayValue()
    {
        if (empty($this->html_value)) {
            return $this->empty_value;
        }

        try {
            return DateTimeImmutableFactory::fromStrictFormat($this->html_value, 'Y-m-d')
                ->format('j M Y');
        } catch (InvalidArgumentException) {
            return 'Invalid date ('.$this->html_value.')';
        }
    }

}
