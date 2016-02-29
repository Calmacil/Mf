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
    {$this->coreLogger()->info('Pouic');
        return array_key_exists($offset, $this->plugins);
    }

    public function offsetGet($offset)
    {$this->coreLogger()->info('Pouet');
        return $this->plugins[$offset];
    }

    public function offsetSet($offset, $value)
    {$this->coreLogger()->info('Plop');
        if (is_a($value, '\\Mf\\Plugin\\PluginInterface')) {
            $this->plugins[$offset] = $value;
            return true;
        }
        return false;
    }

    public function offsetUnset($offset)
    {$this->coreLogger()->info('Pwap');
        if (array_key_exists($offset, $this->plugins)) {
            unset($this->plugins[$offset]);
            return true;
        }
        return false;
    }

    private function iterate($is_a, $func)
    {
        foreach ($this->plugins as $plugin) {
            if (is_a($plugin, $is_a)) {
                $plugin->{$func}();
            }
        }
    }

    public function start()
    {
        $this->iterate('PluginStartInterface', 'start');
    }

    public function before()
    {
        $this->iterate('PluginBeforeInterface', 'before');
    }

    public function after()
    {
        $this->iterate('PluginAfterInterface', 'after');
    }

    public function end()
    {
        $this->iterate('PluginEndInterface', 'end');
    }

}