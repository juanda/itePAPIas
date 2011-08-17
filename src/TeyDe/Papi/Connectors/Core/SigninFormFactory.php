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
class SigninFormFactory
{

    /**
     * @param array $data data retrieved by the login form
     * @param string $connector_name
     * @return string a file with a template to render the form
     */
    public static function createInstance($connector_name)
    {        
        $file = __DIR__.'/../' . $connector_name . "/signinForm.tpl.php";
       
        if (file_exists($file))
        {
            return $file;
        }
       
        return __DIR__.'/signinForm.tpl.php';
    }
}
