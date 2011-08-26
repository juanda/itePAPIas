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

/**
 * You can define configuration parameter in the config/app.yml file of the plugin.
 * It's recommended to add them at the 'eda_papi_plugin_connectors' of the file.
 *
 * You can access these parameter by means of the sfConfig::get() function, like
 * this:
 *
 * $param = sfConfig::get('eda_papi_plugin_connectors_array_prueba', 'valor por defecto');
 *
 * More information about this in the symfony documentation:
 * http://www.symfony-project.org/gentle-introduction/1_4/en/05-Configuring-Symfony
 *
 * Also, if you need the PAPI Request Parameters you can access them like this:
 *
 * $papiParams = sfContext::getInstance() -> getUser()->getAttributeHolder()
 * ->getNames('PAPIREQUEST')
 */

namespace TeyDe\Papi\Connectors\Simple;

class Connector
{

    protected $isAuthenticated = false;
    protected $signinData;
    protected $users = array(
        'anselmo' => array(
            'password' => 'pruebas',
            'attributes' => array(
                'uid' => 'juanda',
                'att1' => 'val1',
                'att2' => array('val21', 'val22'),
        )),
        'rosa' => array(
            'password' => 'pruebas',
            'attributes' => array(
                'uid' => 'paula',
                'att1' => 'val12',
            ),
            ));

    public function __construct($data, $config=null)
    {
//        echo '<pre>';
//        print_r($this -> users);
//        echo '</pre>';
//        exit;
        
        $this->signinData = $data;

        if (isset($this->signinData['username']) && isset($this->signinData['password']))
        {
            $username = $this->signinData['username'];
            $password = $this->signinData['password'];

            if (array_key_exists($username, $this->users))
            {
//                echo $this->users[$username]['password'].$password;exit;
                if($this->users[$username]['password'] == $password)
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
            return $this->users[$this->signinData['username']]['attributes'];
        }
        else
            return null;
    }

}
