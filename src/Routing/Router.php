<?php
/**
 * @author Calmacil <thomas.lenoel@gmail.com>
 * @package \Mf\Core
 * @copyright Calmacil 2016
 * @licence MIT
 */

namespace Mf\Routing;


use Psr\Log\InvalidArgumentException;

class Router
{
    /**
     * Unique instance
     * @var \Mf\Routing\Router
     */
    private static $_instance;

    /**
     * Array of Route objects
     * @var array-of-Route<string>
     */
    private $routes = array();

    /**
     * Router constructor.
     */
    private function __construct()
    {

    }

    public function load($routes_file)
    {
        if (!is_file($routes_file)) {
            throw new InvalidArgumentException("The given routes file does not exist");
        }

        $json = file_get_contents($routes_file);
        $routes = json_decode($json, true);

        if (!$routes) {
            print_r(json_last_error_msg());
        }

        foreach ($routes as $route_name => $route_settings) {
            $this->routes[$route_name] = new Route($route_name, $route_settings);
        }
    }

    /**
     * Instanciator for the singleton
     *
     * @param string $route_file
     * @return Router
     */
    public static function getInstance($route_file = null)
    {
        if (!self::$_instance) {
            self::$_instance = new Router();
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
            throw new \ErrorException("The route $name does not exist.");
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
                return $route;
            }
        }
        return false;
    }
}