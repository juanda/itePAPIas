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
