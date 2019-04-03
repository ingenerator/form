<?php
/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */

namespace Ingenerator\Form\Element;


abstract class AbstractFormElement
{
    protected $schema = [];

    public function __construct(array $schema)
    {
        unset($schema['_comment']);
        unset($schema['type']);

        $default = $this->getDefaultSchema();
        if ($undefined_options = \array_diff(\array_keys($schema), \array_keys($default))) {
            throw new \InvalidArgumentException(
                'Unexpected options for '.\get_class($this).': '.\implode(', ', $undefined_options)
            );
        }

        $schema = \array_merge($default, $schema);

        $empty_options = [];
        foreach ($this->listRequiredSchemaKeys() as $option) {
            if ( ! $schema[$option]) {
                $empty_options[] = $option;
            }
        }
        if ($empty_options) {
            throw new \DomainException(
                'Missing required options for '.\get_class($this).': '.\implode(', ', $empty_options)
            );
        }

        $this->schema = \array_merge($default, $schema);
    }

    protected function getDefaultSchema()
    {
        return [];
    }

    protected function listRequiredSchemaKeys()
    {
        return [];
    }

    public function __get($option)
    {
        if ( ! \array_key_exists($option, $this->schema)) {
            throw new \OutOfBoundsException("No property '$option' on ".\get_class($this));
        }

        return $this->schema[$option];
    }

    public function __set($field, $value)
    {
        throw new \LogicException('Cannot assign '.\get_class($this).'->'.$field);
    }

}
