<?php
/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */

namespace Ingenerator\Form\Util;

/**
 * Wraps an array of form data, providing a simple interface to access nested field values using the
 * HTML field name. Used when rendering application forms and similar customisable forms. Provides
 * empty values for any unknown field.
 *
 * @package Teamdetails\Form
 */
class FormDataArray
{
    private array $data;

    /**
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * @param string $fieldname
     * @param mixed  $value
     *
     * @return bool
     */
    public function matchesValue($fieldname, $value)
    {
        return ($this->getRawValue($fieldname) == $value);
    }

    /**
     * @param string $fieldname
     * @param mixed  $value
     *
     * @return string 'selected' or ''
     */
    public function isSelected($fieldname, $value)
    {
        return $this->matchesValue($fieldname, $value)
            ? 'selected'
            : '';
    }

    /**
     * @param string $fieldname
     *
     * @return mixed
     */
    public function getRawValue($fieldname)
    {
        $path = $this->getFieldPath($fieldname);

        return \Arr::path($this->data, $path, NULL);
    }

    /**
     * @param string $fieldname
     *
     * @return array
     */
    public function getGroupIndices($fieldname)
    {
        $data = $this->getRawValue($fieldname);

        if (\is_array($data)) {
            return \array_keys($data);
        } elseif ($data) {
            throw new \UnexpectedValueException(
                "Cannot get group indices of non_array field '$fieldname"
            );
        } else {
            return [];
        }
    }

    /**
     * @param string $fieldname
     * @param mixed  $value
     */
    public function setFieldValue($fieldname, $value)
    {
        $path = $this->getFieldPath($fieldname);

        // Implementation borrowed from \Arr::set_path but with additional safety for duplicate values
        $array = &$this->data;
        while (\count($path) > 1) {
            $key = \array_shift($path);
            if (\ctype_digit($key)) {
                $key = (int) $key;
            }

            if ( ! isset($array[$key])) {
                $array[$key] = [];
            } elseif ( ! \is_array($array[$key])) {
                throw new \LogicException('Duplicate or overlapping fieldname : '.$fieldname);
            }

            $array = &$array[$key];
        }

        $key = \array_shift($path);
        if (isset($array[$key])) {
            throw new \LogicException('Duplicate or overlapping fieldname : '.$fieldname);
        }
        $array[$key] = $value;
    }

    /**
     * @return array
     */
    public function getValues()
    {
        return $this->data;
    }

    /**
     * @param string $fieldname
     *
     * @return array
     */
    protected function getFieldPath($fieldname)
    {
        if ( ! \preg_match('/^\w+(\[[_\w]+\])*$/', $fieldname, $matches)) {
            throw new \InvalidArgumentException("Invalid fieldname: '$fieldname'");
        }

        $path = \str_replace(']', '', $fieldname);

        return \explode('[', $path);
    }
}
