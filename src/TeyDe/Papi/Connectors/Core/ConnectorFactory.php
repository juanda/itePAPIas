<?php

/*
 * Copyright 2010 Instituto de Tecnologías Educativas - Ministerio de Educación de España
 *
 * Licencia con arreglo a la EUPL, Versión 1.1 exclusivamente
 * (la «Licencia»);
 * Solo podrá usarse esta obra si se respeta la Licencia.
 * Puede obtenerse una copia de la Licencia en:
 *
 * http://ec.europa.eu/idabc/eupl5
 *
 * y también en:

 * http://ec.europa.eu/idabc/en/document/7774.html
 *
 * Salvo cuando lo exija la legislación aplicable o se acuerde
 * por escrito, el programa distribuido con arreglo a la
 * Licencia se distribuye «TAL CUAL»,
 * SIN GARANTÍAS NI CONDICIONES DE NINGÚN TIPO, ni expresas
 * ni implícitas.
 * Véase la Licencia en el idioma concreto que rige
 * los permisos y limitaciones que establece la Licencia.
 */
?>
<?php

namespace TeyDe\Papi\Connectors\Core;


/**
 * This is a factory class used by the signin action to create the connector
 * set in the configuration  (app.yml).
 */
class ConnectorFactory
{

    /**
     * @param array $data data retrieved by the login form
     * @param string $connector_name
     * @return class an object with of the selected $connector_name class
     */
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
