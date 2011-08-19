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

/**
 * If you need the PAPI Request Parameters you can access them like this:
 *
 * $papiParams = sfContext::getInstance() -> getUser()->getAttributeHolder()
 * ->getNames('PAPIREQUEST')
 */

namespace TeyDe\Papi\Connectors\SQL;

use TeyDe\Papi\Core\PAPIASLog;

class Connector
{

    protected $isAuthenticated = false;
    protected $attributes = array();
    protected $signinData;
    protected $sql;
    protected $pdo_conn;

    public function __construct($data, $config)
    {
        $this->connectPDO($config);

        $this->signinData = $data;
        $this->sql = $config['sql'];

        if (isset($this->signinData['username']) && isset($this->signinData['password']))
        {
            $username = $this->signinData['username'];
            $password = $this->signinData['password'];

            $user = $this->findUser($username, $password);
            if ($user)
            {
                $this->isAuthenticated = true;
                foreach ($user as $k => $v)
                {
                    $this->attributes[$k] = $v;
                }
            }
        }
    }

    public function isAuthenticated()
    {
        return $this->isAuthenticated;
    }

    public function getAttributes()
    {
        return $this->attributes;
    }

    protected function connectPDO($config)
    {
        try
        {
            $this->pdo_conn = new \PDO($config['dsn'], $config['dbuser'], $config['dbpass']);
        } catch (\PDOException $e)
        {
            throw new \Exception('SQL connector: - Failed to connect to \'' .
                    $config['dsn'] . '\': ' . $e->getMessage());
        }
    }

    protected function findUser($username, $password)
    {
        $user = $this->query($this->sql, array('username' => $username, 'password' => $password));
        
        // Si no se ha obtenido uno y solo un usuario, entonces
        // hay un error en la autentificación
        if (count($user) != 1)
        {
            return false;
        }


        $msg = 'Symfonite connector: user:' . $username . ' has entered on
                date:' . date('d-m-Y');
        PAPIASLog::doLog($msg);
        return $user[0];
    }

    protected function query($query, $fields)
    {
        try
        {
            $sth = $this->pdo_conn->prepare($query);
        } catch (\PDOException $e)
        {
            throw new \Exception('Symfonite connector: Failed to prepare query: ' . $e->getMessage());
        }

        try
        {
            $res = $sth->execute($fields);
        } catch (\PDOException $e)
        {
            throw new \Exception('edae3auth connector: Failed to execute query: ' . $e->getMessage());
        }

        $data = $sth->fetchAll(\PDO::FETCH_ASSOC);

        return $data;
    }

}
