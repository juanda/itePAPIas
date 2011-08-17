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
