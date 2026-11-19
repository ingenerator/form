<?php
/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */

namespace Ingenerator\Form\Element\Field;

use DateTimeImmutable;
use Exception;
use Ingenerator\Form\Util\FormDataArray;
use Ingenerator\PHPUtils\DateTime\DateString;
use Ingenerator\PHPUtils\DateTime\DateTimeImmutableFactory;
use InvalidArgumentException;


class DateField extends AbstractFormField
{

    public function validateConstraintSchema(array $constraints)
    {
        $min = $this->parseDateConstraintValue($constraints, 'min');
        $max = $this->parseDateConstraintValue($constraints, 'max');
        if ($min && $max && $max < $min) {
            throw new InvalidArgumentException(
                sprintf(
                    "max must be greater than min (got min=%s, max=%s for %s)",
                    DateString::ymd($max),
                    DateString::ymd($min),
                    $this->name,
                ),
            );
        }
    }

    private function parseDateConstraintValue(array $constraints, string $key): ?DateTimeImmutable
    {
        $value = $constraints[$key] ?? null;
        if ($value === null) {
            return null;
        }

        if (!is_string($value)) {
            throw new InvalidArgumentException(
                sprintf(
                    '%s constraint must be a string, got %s for %s',
                    $key,
                    get_debug_type($value),
                    $this->name,
                ),
            );
        }

        try {
            return DateTimeImmutableFactory::fromStrictFormat($value, 'Y-m-d');
        } catch (Exception $e) {
            throw new InvalidArgumentException(
                sprintf('%s constraint must be a valid Y-m-d date string, got %s for %s', $key, $value, $this->name),
                previous: $e,
            );
        }
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
