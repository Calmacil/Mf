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
    private $data = array();

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        if (isset($this->data[$offset]))
            unset($this->data[$offset]);
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        $this->data[$offset] = $value;
    }

    /**
     * @param mixed $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        if (!isset($this->data[$offset]))
            throw new \OutOfBoundsException("Requested key does not exist");
        return $this->data[$offset];
    }

    /**
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->data);
    }

    public function __set($name, $value)
    {
        $this->data[$name] = $value;
    }

    public function __get($name)
    {
        if (!isset($this->data[$name]))
            throw new \OutOfBoundsException("Requested key does not exist");
        return $this->data[$name];
    }
}