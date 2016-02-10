<?php
/**
 * @author Calmacil <thomas.lenoel@gmail.com>
 * @package \Mf\Http
 * @copyright Calmacil 2016
 * @licence MIT
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

    /**
     * @var \stdClass
     */
    protected $conf;

    public function __construct($app)
    {
        $this->app = $app;
        $this->conf = Config::get($this->app->cfile);
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