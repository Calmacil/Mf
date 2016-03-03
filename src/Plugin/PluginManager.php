<?php
/**
 * Created by PhpStorm.
 * @author calmacil
 *
 * This file is a part of the Mf project. All rights reserved.
 */

namespace Calma\Mf\Plugin;


class PluginManager implements \ArrayAccess
{
    /**
     * @var string
     */
    private $root;

    /**
     * @var array-of-PluginInterface
     */
    protected $plugins = array();

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
        if ($value instanceof \Calma\Mf\Plugin\PluginInterface) {
            $this->plugins[$offset] = $value;
            return true;
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

    private function iterate($is_a, $func)
    {
        foreach ($this->plugins as $pname => $plugin) {
            if ($plugin instanceof $is_a) {
                $this->coreLogger()->debug('Called {plugin}::{func}()', ['plugin' => $pname, 'func' => $func]);
                $plugin->{$func}();
            } else {
                $this->coreLogger()->debug('{func} not called on {plugin}', ['plugin' => $pname, 'func' => $func]);
            }
        }
    }

    public function start()
    {
        $this->coreLogger()->notice("Executing *start* actions");
        $this->iterate('\\Calma\\Mf\\Plugin\\PluginStartInterface', 'start');
    }

    public function before()
    {
        $this->coreLogger()->notice("Executing *before* actions.");
        $this->iterate(PluginBeforeInterface, 'before');
    }

    public function after()
    {
        $this->coreLogger()->notice("Executing *after* actions.");
        $this->iterate('PluginAfterInterface', 'after');
    }

    public function end()
    {
        $this->coreLogger()->notice("Executing *end* actions.");
        $this->iterate('PluginEndInterface', 'end');
    }

}