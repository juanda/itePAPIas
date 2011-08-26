<?php

namespace TeyDe\Papi\Filters;

/**
 * Configuration:
 *
 *  array(
 *           'class_name' => 'AttributeReverse',
 *           'config' => array(),
 *       ),
 */

class AttributeReverse
{

    public static function execute($attributes, $configuration)
    {

        foreach ($attributes as $ka => $va)
        {
            if (is_array($va))
            {
                foreach ($va as $k => $v)
                {
                    $attributes[$ka][$k] = strrev($attributes[$ka][$k]);
                }
            } else
            {
                $attributes[$ka] = strrev($attributes[$ka]);
            }
        }
        return $attributes;
    }

}
