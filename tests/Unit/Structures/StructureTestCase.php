<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use StackPay\Payments\Structures;

class StructureTestCase extends TestCase
{
    protected $struct = "INVALID";

    public function full($name, $type, $hasCreate = false)
    {
        $this->getter($name);

        $this->setter($name, $type);

        if ($hasCreate) {
            $this->create($name, $type);
        }
    }

    public function getter($name)
    {
        $struct = new $this->struct;

        $input = "test $name";

        $struct->$name = $input;

        $returned = $struct->$name();

        $this->assertEquals($input, $returned, "Asserting that $name() returns $name");
    }

    public function setter($name, $type)
    {
        $this->setter_notNull($name, $type);

        $this->setter_null($name, $type);
    }

    public function setter_notNull($name, $type)
    {
        $struct = new $this->struct;

        $input = $this->getTestValueByType($type);

        $function = "set". ucfirst($name);

        $returned = $struct->$function($input);

        $this->assertEquals($input, $struct->$name, "Asserting the $function($type) set $name to $type");
        $this->assertEquals($struct, $returned);
    }

    public function setter_false($name, $type)
    {
        $struct = new $this->struct;

        $function = "set". ucfirst($name);

        $returned = $struct->$function(false);

        $this->assertFalse($struct->$name, "Asserting the $function(null) set $name to false");
        $this->assertEquals($struct, $returned);
    }

    public function setter_null($name, $type)
    {
        $struct = new $this->struct;

        $function = "set". ucfirst($name);

        $returned = $struct->$function(null);

        $this->assertNull($struct->$name, "Asserting the $function(null) set $name to null");
        $this->assertEquals($struct, $returned);
    }

    public function create($name, $type)
    {
        $this->create_doesntExist($name, $type);
        $this->create_exists($name, $type);
    }

    public function create_doesntExist($name, $type)
    {
        $struct = new $this->struct;

        $function = "create". ucfirst($name);

        $returned = $struct->$function();

        $this->assertEquals(new $type, $returned, "Asserting $function returned a new $type");
        $this->assertEquals(new $type, $struct->$name, "Asserting $function set $name to a new $type");
    }

    public function create_exists($name, $type)
    {
        $struct = new $this->struct;

        $input = \Mockery::mock($type);

        $struct->$name = $input;

        $function = "create". ucfirst($name);

        $returned = $struct->$function();

        $this->assertEquals($input, $returned);
        $this->assertEquals($input, $struct->$name);
    }

    public function getTestValueByType($type)
    {
        switch ($type) {
            case "bool":
                return true;

            case "int":
                return rand();

            case "string":
                return uniqid();

            default:
                return \Mockery::mock($type);
        }
    }
}
