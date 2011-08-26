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

use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints;

class SigninParameters
{

    public $usuario;
    public $clave;
    public $dni;
  

    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraint('usuario', new Constraints\NotBlank());
        $metadata->addPropertyConstraint('clave', new Constraints\NotBlank());
        $metadata->addPropertyConstraint('dni'     , new Constraints\NotBlank());
    }
    
    public function bind($data)
    {
        $this->usuario = $data['usuario'];
        $this->clave = $data['clave'];
        $this->dni      = $data['dni'];
    }

}
