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

namespace TeyDe\Papi\Connectors\SimpleWithForm;

class Connector
{

    protected $isAuthenticated = false;
    protected $signinData;
    protected $users = array(
        'anselmo' => array(
            'clave' => 'pruebas',
            'attributes' => array(
                'uid' => 'juanda',
                'att1' => 'val1',
                'att2' => array('val21', 'val22'),
        )),
        'rosa' => array(
            'clave' => 'pruebas',
            'attributes' => array(
                'uid' => 'paula',
                'att1' => 'val12',
            ),
            ));

    public function __construct($data, $config=null)
    {      
        $this->signinData = $data;

        if (isset($this->signinData['usuario']) && isset($this->signinData['clave']))
        {
            $username = $this->signinData['usuario'];
            $password = $this->signinData['clave'];

            if (array_key_exists($username, $this->users))
            {
                if($this->users[$username]['clave'] == $password)
                {                    
                    $this->isAuthenticated = true;
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
        if ($this->isAuthenticated)
        {
            return $this->users[$this->signinData['usuario']]['attributes'];
        }
        else
            return null;
    }

}
