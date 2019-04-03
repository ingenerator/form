<?php
/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */

namespace Ingenerator\Form\Criteria;

/**
 * Tests a field value against a set of criteria to decide whether or not it matches. Used for marking
 * fields to highlight or hide in form displays etc.
 *
 * @package Teamdetails\Form
 */
class FieldCriteriaMatcher
{

    /**
     * Indicates if the value provided matches any of the provided criteria
     *
     *     $matcher->matches('something', ['empty', 'not_empty', 'value:something']); // TRUE
     *     $matcher->matches('', ['empty']) // TRUE
     *
     * @param mixed $value
     * @param array $criteria
     *
     * @return bool
     */
    public function matches($value, array $criteria)
    {
        foreach ($criteria as $criterion) {
            if ($this->matchesCriterion($value, $criterion)) {
                return TRUE;
            }
        }

        return FALSE;
    }

    /**
     * @param string $value
     * @param string $criterion
     *
     * @return bool
     */
    protected function matchesCriterion($value, $criterion)
    {
        $parts = \explode(':', $criterion);
        $rule  = \array_shift($parts);

        switch ($rule) {
            case 'empty':
                return $this->isEmpty($value);

            case 'not_empty':
                return ! $this->isEmpty($value);

            case 'value':
                return $value === \implode(':', $parts);

            default:
                throw new \InvalidArgumentException("Unknown criteria type '$criterion'");
        }
    }

    /**
     * @param string $value
     *
     * @return bool
     */
    protected function isEmpty($value)
    {
        return (
            ($value === NULL)
            OR (\trim($value) === '')
        );
    }
}
