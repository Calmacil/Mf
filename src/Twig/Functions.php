<?php
/**
 * Created by PhpStorm.
 * @author calmacil
 *
 * This file is a part of the Mf project. All rights reserved.
 */

namespace Calma\Mf\Twig;


use Calma\Mf\Routing\Router;

class Functions
{
    /**
     * @param \Twig_Environment $env
     * @param mixed $route_name
     * @param mixed $params
     * @return string
     */
    public static function url(\Twig_Environment $env, $route_name, $params=null)
    {
        return Router::getInstance()->generateRoute($route_name, $params);
    }
}