<?php
/**
 * @author calmacil <thomas.lenoel@gmail.com>
 * @package \Mf\Core
 * @copyright Calmacil 2016
 * @licence MIT
 */

namespace Mf\Core;


use MongoDB\Driver\Exception\Exception;
use Psr\Log\InvalidArgumentException;

class Request
{
    /**
     * @var string
     */
    private $uri;

    /**
     * @var Mf\Routing\Route
     */
    private $route;

    /**
     * @var array-of-mixed
     */
    private $post_params = array();

    /**
     * Request constructor
     */
    public function __construct($uri)
    {
        $this->uri = $uri;

        foreach ($_POST as $key => $value) {
            $this->post_params[$key] = addslashes(htmlspecialchars(trim($value)));
        }
    }

    /**
     * Sets the correct route corresponding to the request URI
     * @throws Exception
     */
    public function proceed()
    {
        $this->route = Router::getInstance()->search($this->uri);
        if (!$this->route) {
            throw new \Exception("Impossible to create the Route object for route {$this->route}");
        }
    }

    /**
     * @param $key
     * @return mixed
     */
    public function post($key)
    {
        if (!array_key_exists($key, $this->post_params)) {
            throw new InvalidArgumentException("Parameter $key does not exist in the request POST parameters");
        }
        return $this->post_params[$key];
    }

    /**
     * @param $key
     * @return mixed
     */
    public function get($key)
    {
        return $this->route->getHttpParams($key);
    }

    /**
     * @return string
     */
    public function getController()
    {
        return $this->route->getController();
    }

    /**
     * @return string
     */
    public function getAction()
    {
        return $this->route->getAction();
    }
}