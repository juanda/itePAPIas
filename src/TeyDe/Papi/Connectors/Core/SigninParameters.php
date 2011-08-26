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

use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints;

class SigninParameters
{

    protected $username;
    protected $password;
   
    public function getUsername()
    {
        return $this->username;
    }

    public function setUsername($u)
    {
        $this->username = $u;
    }
   
    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($p)
    {
        $this->password = $p;
    }

    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraint('username', new Constraints\NotBlank());
        $metadata->addPropertyConstraint('password', new Constraints\NotBlank());
    }
    
    public function bind($data)
    {
        $this->username = $data['username'];
        $this->password = $data['password'];
    }

}
