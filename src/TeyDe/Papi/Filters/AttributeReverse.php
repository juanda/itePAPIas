<?php
/*
  This file is part of itePAPIas.
  Foobar is free software: you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation, either version 3 of the License, or
  (at your option) any later version.

  Foobar is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with Foobar.  If not, see <http://www.gnu.org/licenses/>.
 */
?>
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
