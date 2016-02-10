<?php
/**
 * Created by PhpStorm.
 * @author calmacil
 *
 * This file is a part of the Mf project. All rights reserved.
 */

namespace Mf;


use Psr\Log\InvalidArgumentException;

class Controller
{
    protected $template;

    /**
     * @var Application
     */
    protected $app;

    /**
     * Controller constructor.
     * @param Application $app
     */
    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * @param string $action_name
     * @return mixed
     */
    public function getTemplate($action_name=null)
    {
        if (is_array($this->template)) {
            if (!$action_name)
                throw new InvalidArgumentException("\$action_name needs to be specifide");

            if (!array_key_exists($action_name, $this->template))
                throw new InvalidArgumentException("Key $action_name not found in the templates list");

            return $this->template[$action_name];
        }

        return $this->template;
    }

    /**
     * @param string $param
     * @return mixed
     */
    public function get($param)
    {
        return $this->app->getRequest()->get($param);
    }

    /**
     * @param string $param
     * @return mixed
     */
    public function post($param)
    {
        return $this->app->getRequest()->post($param);
    }

    /**
     * @todo implement
     */
    public function display404()
    {

    }
}