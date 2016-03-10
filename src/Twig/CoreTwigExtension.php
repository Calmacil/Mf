<?php
/**
 * Created by PhpStorm.
 * @author calmacil
 *
 * This file is a part of the Mf project. All rights reserved.
 */

namespace Calma\Mf\Twig;

use Calma\Mf\Routing\Router;


class CoreTwigExtension extends \Twig_Extension
{
    public function getName()
    {
        return "mf_core_ext";
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('url', [$this, 'url'])
        ];
    }

    public function url($route_name, $params=array())
    {
        return Router::getInstance()->generateRoute($route_name, $params);
    }
}