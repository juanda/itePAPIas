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

namespace TeyDe\Papi\Connectors\Core;

class ConnectorFactory
{    
    public static function createInstance($data, $connector_name, $config=null)
    {        
        $class = '\\TeyDe\\Papi\\Connectors\\'. $connector_name . "\\Connector";
        
        if (!class_exists($class))
        {            
            throw new \RuntimeException('the connector ' . $class . ' does not exits');
        }

        $connector = new $class($data, $config);

        return $connector;
    }

}
