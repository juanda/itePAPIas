<?php

namespace TeyDe\Papi\Filters;

/**
 * Configuration:
 *
 *      array(
 *           'class_name' => 'AttributePrune',
 *           'config' => array(
 *               'attributes_to_prune' => array('att1', 'ePa'),
 *           ),
 *       ),
 */
class AttributePrune
{
    public static function execute($attributes, $configuration)
    {        
        $atts = $configuration['attributes_to_prune'];
       
        foreach ($attributes as $ka => $va)
        {
            foreach ($atts as $vm)
            {
                if($ka == $vm)
                {                                        
                    unset($attributes[$ka]);
                }
            }
        } 
        return $attributes;

    }
}
