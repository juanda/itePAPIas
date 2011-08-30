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

namespace TeyDe\Papi\Connectors\Symfonite;

use TeyDe\Papi\Core\PAPIASLog;

class Connector
{

    protected $isAuthenticated = false;
    protected $signinData;
    protected $sfGuardUser;
    protected $pdo_conn;

    public function __construct($data, $config)
    {
        $this->connectPDO($config);

        $this->signinData = $data;

        if (isset($this->signinData['username']) && isset($this->signinData['password']))
        {           
            $username = $this->signinData['username'];
            $password = $this->signinData['password'];

            $sfGuardUser = $this->findSfGuardUser($username, $password);
            if ($sfGuardUser)
            {
                $this->isAuthenticated = true;
                $this->sfGuardUser = $sfGuardUser;
            }
        }
    }

    public function isAuthenticated()
    {
        return $this->isAuthenticated;
    }

    public function getAttributes()
    {
        $attributes = array();

        $attributes['uid'] = $this->sfGuardUser['username'] ;
        $attributes['id_sfuser'] = $this->sfGuardUser['id'];

        /* Extract attributes. We allow the resultset to consist of multiple rows. Attributes
         * which are present in more than one row will become multivalued. NULL values and
         * duplicate values will be skipped. All values will be converted to strings.
         */
        /////////////////////////
        // ATRIBUTOS PERSONALES//
        /////////////////////////
        $query = "SELECT p.*,u.* from eda_personas as p, eda_usuarios as u
                  WHERE u.id = :id_usuario and u.id_persona=p.id";

        $persona = $this->query($query, array('id_usuario' => $this->sfGuardUser['id_usuario']));

        if (!is_null($persona[0]['nombre']) && $persona[0]['nombre'] != '')
            $attributes['cn'] = $persona[0]['nombre'];
        if (!is_null($persona[0]['apellido1']) && $persona[0]['apellido1'] != '')
        {
            $attributes['schacSn1'] = $persona[0]['apellido1'];
            $attributes['sn'] = $persona[0]['apellido1'];
        }
        if (!is_null($persona[0]['apellido2']) && $persona[0]['apellido2'] != '')
            $attributes['schacSn2'] = $persona[0]['apellido2'];
        if (!is_null($persona[0]['alias']) && $persona[0]['alias'] != '')
            $attributes['eduPersonNickname'] = $persona[0]['alias'];
        if (!is_null($persona[0]['sexo']) && $persona[0]['sexo'] != '')
            $attributes['schacGender'] = $persona[0]['sexo'];
        if (!is_null($persona[0]['fechanacimiento']) && $persona[0]['fechanacimiento'] != '')
            $attributes['schacDateOfBirth'] = $persona[0]['fechanacimiento'];

        ////////////////////////////
        //PERMISOS EN APLICACIONES//
        ////////////////////////////

        $query = "SELECT c.*, ap.codigo
                  FROM eda_credenciales  as c, eda_perfil_credencial as pc, eda_accesos as a, eda_aplicaciones as ap
                  WHERE a.id_usuario = :id_usuario
                  AND a.id_perfil = pc.id_perfil
                  AND pc.id_credencial=c.id
                  AND c.id_aplicacion=ap.id
                  ORDER by c.nombre";

        $credenciales = $this->query($query, array('id_usuario' => $this->sfGuardUser['id_usuario']));

        foreach ($credenciales as $credencial)
        {
            $attributes['eduPersonEntitlement'][] = $credencial['codigo'] . ':' . $credencial['nombre'];
        }


        ///////////
        //ÁMBITOS//
        ///////////

        $query = "SELECT a.codigo as cod_amb, p.codigo as cod_per,
                 at.nombre as tipo_amb
                 FROM eda_ambitos AS a, eda_ambitostipos as at,
                 eda_acceso_ambito AS aa, eda_accesos AS ac, eda_periodos as p
                 WHERE ac.id_usuario = :id_usuario
                 AND ac.id = aa.id_acceso
                 AND aa.id_ambito = a.id
                 AND a.id_ambitotipo = at.id
                 AND a.id_periodo = p.id
                 AND a.estado ='ACTIVO'
                 AND (aa.fechacaducidad IS NULL OR aa.fechacaducidad > '" . date('Y-m-d') . "')
";

        $ambitos = $this->query($query, array('id_usuario' => $this->sfGuardUser['id_usuario']));

        foreach ($ambitos as $ambito)
        {
            $attributes['ambitos'][] = $ambito['cod_per'] . ':' . $ambito['tipo_amb'] . ':' . $ambito['cod_amb'];
        }

        ///////////////////////
        //ATRIBUTOS EXTRAEDAE//
        ///////////////////////
        $query = "SELECT ua.nombre, uav.valor FROM eda_usu_atributos AS ua, eda_usu_atributos_valores AS uav
                    WHERE uav.id_usuario = :id_usuario
                    AND uav.id_usu_atributo = ua.id
                    AND (uav.expira IS NULL OR uav.expira > '" . date('Y-m-d') . "')";


        $data = $this->query($query, array('id_usuario' => $this->sfGuardUser['id_usuario']));


        foreach ($data as $row)
        {
            $attributes[$row['nombre']][] = $row['valor'];
        }
        
        return $attributes;
    }

    protected function connectPDO($config)
    {
        try
        {
            $this->pdo_conn = new \PDO($config['dsn'], $config['dbuser'], $config['dbpass']);
        } catch (\PDOException $e)
        {
            throw new \Exception('Symfonite connector: - Failed to connect to \'' .
                    $config['dsn'] . '\': ' . $e->getMessage());
        }
    }

    protected function findSfGuardUser($username, $password)
    {
        $query = 'SELECT sfgu.*, u.id as id_usuario FROM sf_guard_user as sfgu, eda_usuarios as u
                         where sfgu.username = :username and sfgu.id=u.id_sfuser';

        $user = $this->query($query, array('username' => $username));


        // Si no se ha obtenido uno y solo un usuario, entonces
        // hay un error en la autentificación
        if (count($user) != 1)
        {
            return false;
        }

        // Estraemos los datos necesarios para comprobar el password
        $salt = $user[0]['salt'];
        $algorithm = $user[0]['algorithm'];
        $pass = $user[0]['password'];
        $uid = $user[0]['username'];
        $id_sfuser = $user[0]['id'];
        $id_usuario = $user[0]['id_usuario'];

        if (!is_callable($algorithm))
        {
            throw new \Exception('Symfonite connector:' . $algorithm . ' is not a callable function: ');
        }

        if ($pass != call_user_func_array($algorithm, array($salt . $password)))
        {
            $msg = 'Symfonite connector: user:' . $username . ' has entered
                an wrong password';
            PAPIASLog::doLog($msg);

            return false;
        } else
        {
            $msg = 'Symfonite connector: user:' . $username . ' has entered on
                date:' . date('d-m-Y');
            PAPIASLog::doLog($msg);
            return $user[0];
        }
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
