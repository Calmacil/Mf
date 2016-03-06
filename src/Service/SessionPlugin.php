<?php
/**
 * @author Calmacil <thomas.lenoel@gmail.com>
 * @package Calma\Mf\Service
 * @copyright Calmacil 2016
 * @licence MIT
 */
 
namespace Calma\Mf\Service;

use Calma\Mf\Plugin\PluginInterface;
use Calma\Mf\Plugin\PluginStartInterface;
use Calma\Mf\Plugin\PluginEndInterface;

class SessionPlugin implements PluginInterface
{
    private $container = array();
 
    /**
     * @var \Calma\Mf\Application
     */
    private $app;
    
    /**
     * @var mixed Options useale by the plugin
     */
    private $sess_name = 'MF_SESSION';
    
    /**
     * Session plugin constructor.
     * 
     * Inits the session if not already done and then tries to load and decrypt
     *  stored session variables.
     */
    public function __construct(&$app, $options=null)
    {
        $this->app = $app;
        if ($options && isset($options->session_name))
            $this->sess_name = $options->session_name;
        
        
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        
        if (isset($_SESSION['MF_SESSION'])) {
            $this->container = unserialize(base64_decode($_SESSION['MF_SESSION']));
        }
    } 
    
    /**
     * Called at the end of the PHP session
     * Stores all session variables after encryption
     */
    public function __destruct()
    {
        if ($this->container) {
            $_SESSION['MF_SESSION'] = base64_encode(serialize($this->container));
        }
    }
    
    /**
     * Returns a session variable
     * @param string $key
     * @return mixed
     */
    public function get($key)
    {
        return array_key_exists($key, $this->container) ? $this->container[$key] : false;
    }
    
    /**
     * Sets a session variable
     * @param string $key
     * @param mixed $value
     */
    public function set($key, $value)
    {
        $this->container[$key] = $value;
    }

    public function remove($key)
    {
        $this->container[$key] = null;
    }
}
