<?php

namespace StackPay\Payments\Structures;

use StackPay\Payments\Interfaces;

class Customer implements Interfaces\Customer
{
    public $id;
    public $firstName;
    public $lastname;

    public function __construct($id = null)
    {
        $this->setID($id);
    }

    public function id()
    {
        return $this->id;
    }

    public function firstName()
    {
        return $this->firstName;
    }

    public function lastName()
    {
        return $this->lastName;
    }

    //------

    public function setID($id = null)
    {
        $this->id = $id;

        return $this;
    }
    
    public function setFirstName($firstName = null)
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function setLastName($lastName = null)
    {
        $this->lastName = $lastName;

        return $this;
    }
}
