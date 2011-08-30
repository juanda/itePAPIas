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
