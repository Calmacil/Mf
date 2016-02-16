<?php
/**
 * @author Calmacil <thomas.lenoel@gmail.com>
 * @package \Mf\Http
 * @copyright Calmacil 2016
 * @licence MIT
 */

namespace Mf\Routing;


use Mf\Application;
use Mf\Config;
use Psr\Log\InvalidArgumentException;

class Router
{
    /**
     * Unique instance
     * @var \Mf\Routing\Router
     */
    private static $_instance;

    /**
     * @var Application
     */
    private $app;

    /**
     * Array of Route objects
     * @var array-of-Route<string>
     */
    private $routes = array();

    /**
     * Router constructor.
     * @param Application $app
     */
    private function __construct($app)
    {
        $this->app = $app;
    }

    public function load($routes_file)
    {
        $this->app->coreLogger()->addInfo("Loading routes.");
        $routes = function_exists('get_object_vars') ?
            get_object_vars(Config::get(Config::get($this->app->cfile)->paths->routing_file)) :
            $this->readJson(ROOT . "/config/$routes_file.json");

        foreach ($routes as $route_name => $route_settings) {
            try {
                $this->routes[$route_name] = new Route($route_name, $route_settings);
            } catch(InvalidArgumentException $e) {
                $this->app->coreLogger()->addError("Router error: {message}\n", array('message' => $e->getMessage()));
            }
        }
    }

    /**
     * fallback in case of get_object_vars would be disabled.
     * Ideally, not used
     *
     * @param $routes_file
     * @return mixed
     */
    private function readJson($routes_file)
    {
        $json = file_get_contents($routes_file);
        $routes = json_decode($json, true);

        if (!$routes) {
            $this->app->coreLogger()->addError("Could not read JSON route file: {message}",
                array('message' => json_last_error_msg()));
        }

        return $routes;
    }

    /**
     * Instanciator for the singleton
     *
     * @param string $route_file
     * @param Application $app;
     * @return Router
     */
    public static function getInstance($route_file = null, $app = null)
    {
        if (!self::$_instance) {
            if (!$app) {
                throw new \Exception("Router needs an Application instance to be initialized.");
            }
            self::$_instance = new Router($app);
            if ($route_file) {
                self::$_instance->load($route_file);
            }
        }
        return self::$_instance;
    }

    /**
     * @param $name
     * @return Route
     * @throws \ErrorException
     */
    public function getRoute($name)
    {
        if (!array_key_exists($name, $this->routes)) {
            $this->app->coreLogger()->addWarning("The route {name} does not exist.", array('name' => $name));
        }
        return $this->routes[$name];
    }

    /**
     * Generates the given route with given params
     *
     * @param $name                 The route's name
     * @param array|null $params    The route's params to insert
     * @return string
     * @throws \ErrorException
     */
    public function generateRoute($name, $params = null)
    {
        $this->app->coreLogger()->addInfo("Generating route {name}", array('name' => $name));
        return $this->getRoute($name)->generate($params);
    }

    /**
     * Searches a matching route for the given uri
     *
     * @param string $uri
     * @return bool|\Mf\Routing\Route
     */
    public function search($uri)
    {
        foreach ($this->routes as $route) {
            if ($route->check($uri)) {
                $this->app->coreLogger()->addNotice("Found matching route {name}", array('name' => $route->getName()));
                return $route;
            }
            $this->app->coreLogger()->addDebug("Route {name} does't match.", array('name'=>$route->getName()));
        }
        return false;
    }
}