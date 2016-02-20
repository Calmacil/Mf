<?php
/**
 * Created by PhpStorm.
 * @author calmacil
 *
 * This file is a part of the Mf project. All rights reserved.
 */

namespace Mf\Plugin;


class PluginManager implements \ArrayAccess, \Iterator
{
    /**
     * @var string
     */
    private $root;

    /**
     * @var array-of-PluginInterface
     */
    private $plugins = array();

    /**
     * PluginManager constructor.
     * @param string $root
     */
    public function __construct($root)
    {
        $this->root = $root;
    }

    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->plugins);
    }

    public function offsetGet($offset)
    {
        return $this->plugins[$offset];
    }

    public function offsetSet($offset, $value)
    {
        if (is_a($value, '\\Mf\\Plugin\\PluginInterface')) {
            $this->plugins[$offset] = $value;
        }
        return false;
    }

    public function offsetUnset($offset)
    {
        if (array_key_exists($offset, $this->plugins)) {
            unset($this->plugins[$offset]);
            return true;
        }
        return false;
    }

    public function next()
    {
        // TODO: Implement next() method.
    }

    public function valid()
    {
        // TODO: Implement valid() method.
    }

    public function current()
    {
        // TODO: Implement current() method.
    }

    public function rewind()
    {
        // TODO: Implement rewind() method.
    }

    public function key()
    {
        // TODO: Implement key() method.
    }

}