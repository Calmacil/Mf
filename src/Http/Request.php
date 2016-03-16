<?php
/**
 * @author calmacil <thomas.lenoel@gmail.com>
 * @package \Mf\Http
 * @copyright Calmacil 2016
 * @licence MIT
 */

namespace Calma\Mf\Http;


use \Calma\Mf\Application;
use \Calma\Mf\Routing\Router;
use \Exception;

class Request
{
    /**
     * @var string
     */
    private $uri;

    /**
     * @var \Calma\Mf\Routing\Route
     */
    private $route;

    /**
     * @var array-of-mixed
     */
    private $post_params = array();

    /**
     * @var Application
     */
    private $app;

    /**
     * Request constructor
     * @param Application $app;
     *
     */
    public function __construct($app, $uri)
    {
        $this->app = $app;
        $this->uri = $uri;

        foreach ($_POST as $key => $value) {
            $this->post_params[$key] = addslashes(htmlspecialchars(trim($value)));
        }
        $this->app->coreLogger()->addNotice("Request initialized.");
    }

    /**
     * Sets the correct route corresponding to the request URI
     * @throws Exception
     */
    public function proceed()
    {
        $this->app->coreLogger()->addInfo("Proceeding with Request treatment.");
        $this->route = Router::getInstance()->search($this->uri);
        if (!$this->route) {
            $this->app->coreLogger()
                ->addCritical("Impossible to pursue proceeding: route unavailable for query string \"{query}\".",
                    array('query' => $this->uri));
            throw new \Exception("Impossible to create the Route object for query string {$this->uri}");
        }
    }

    /**
     * @param $key
     * @return mixed
     */
    public function post($key)
    {
        if (!array_key_exists($key, $this->post_params)) {
            //throw new InvalidArgumentException("Parameter $key does not exist in the request POST parameters");
            $this->app->coreLogger()->warn("Parameter {key} not found if _POST request params.", ['key' => $key]);
        }
        return $this->post_params[$key];
    }

    /**
     * @param $key
     * @return mixed
     */
    public function get($key)
    {
        if (($val = $this->route->getHttpParams()) === false) {
            $this->app->coreLogger()->warn("Parameter {key} not found in _GET request params.", ['key' => $key]);
        }
        return $val;
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

    public function getRoute()
    {
        return $this->route;
    }
}