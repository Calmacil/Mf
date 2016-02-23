<?php
/**
 * @author Calmacil <thomas.lenoel@gmail.com>
 * @package \Mf\Http
 * @copyright Calmacil 2016
 * @licence MIT
 */

namespace Calma\Mf;


class DataObject implements \ArrayAccess
{

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        if (!property_exists($this, "_$offset"))
            throw new \OutOfBoundsException("Requested key does not exist.");
        $this->{"_$offset"} = null;
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        if (!property_exists($this, "_$offset"))
            throw new \OutOfBoundsException("Requested key does not exist.");
        $this->{"_$offset"} = $value;
    }

    /**
     * @param mixed $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        if (!property_exists($this, "_$offset"))
            throw new \OutOfBoundsException("Requested key does not exist");
        return $this->{"_$offset"};
    }

    /**
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return property_exists($this, "_$offset");
    }

    public function __set($name, $value)
    {
        if (!property_exists($this, "_$name"))
            throw new \OutOfBoundsException("Requested property does not exist");
        $this->{"_$name"} = $value;
    }

    public function __get($name)
    {
        if (!property_exists($this, "_$name"))
            throw new \OutOfBoundsException("Requested property does not exist");
        return $this->{"_$name"};
    }
}