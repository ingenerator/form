<?php
/**
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @licence   proprietary
 */

namespace Ingenerator\Form\Util;


/**
 * Supports view-rendering for the showgroups JS plugin
 *
 * @package View\Helper
 */
class ShowgroupHelper
{

    /**
     * @var string
     */
    protected $current_value;

    /**
     * @param string $current_group
     */
    public function __construct($current_group)
    {
        $this->current_value = $current_group;
    }

    /**
     * @param string[] $groups
     *
     * @return string
     */
    public function attrsForGroup(array $groups)
    {
        $attributes = 'data-showgroups="'.\implode(',', $groups).'"';

        if ( ! \in_array($this->current_value, $groups)) {
            $attributes .= ' style="display:none;"';
        }

        return $attributes;
    }
}
