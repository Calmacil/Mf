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
    protected function get($param)
    {
        return $this->app->getRequest()->get($param);
    }

    /**
     * @param string $param
     * @return mixed
     */
    protected function post($param)
    {
        return $this->app->getRequest()->post($param);
    }

    /**
     * send a 404 http code
     * @return bool
     */
    protected function display404()
    {
        if(isset($this->conf->page404)) {
            return $this->app->getResponse()->display404($this->conf->page404);
        }
        return $this->app->getResponse()->display404();
    }

    /**
     * Detailed debug information
     *
     * @param string $msg
     * @param array $context
     */
    protected function debug($msg, $context=array())
    {
        $this->app->appLogger()->addDebug($msg, $context);
    }

    /**
     * Interesting events. Example: user logs, SQL logs.
     *
     * @param string $msg
     * @param array $context
     */
    protected function info($msg, $context=array())
    {
        $this->app->appLogger()->addInfo($msg, $context);
    }

    /**
     * Normal but significant events.
     *
     * @param string $msg
     * @param array $context
     */
    protected function notice($msg, $context=array())
    {
        $this->app->appLogger()->addNotice($msg, $context);
    }

    /**
     * Exceptional occurrences that are not errors. Examples: use of deprecated APIs, poor use of an API, undesirable
     * things that are not necessarily wrong.
     *
     * @param string $msg
     * @param array $context
     */
    protected function warning($msg, $context=array())
    {
        $this->app->appLogger()->addWarning($msg, $context);
    }

    /**
     * Runtime errors that do not require immediate action but should typically be logged and monitored.
     *
     * @param string $msg
     * @param array $context
     */
    protected function error($msg, $context=array())
    {
        $this->app->appLogger()->addError($msg, $context);
    }

    /**
     * Critical conditions. Example: Application component unavailable, unexpected exception.
     *
     * @param string $msg
     * @param array $context
     */
    protected function critical($msg, $context=array())
    {
        $this->app->appLogger()->addCritical($msg, $context);
    }

    /**
     * Action must be taken immediately. Example: entire website down, database unavailable, etc. This should trigger
     * the SMS alert and wake you up.
     *
     * @param string $msg
     * @param array $context
     */
    protected function alert($msg, $context=array())
    {
        $this->app->appLogger()->addAlert($msg, $context);
    }

    /**
     * Emergency: system is unusable.
     *
     * @param string $msg
     * @param array $context
     */
    protected function emergency($msg, $context=array())
    {
        $this->app->appLogger()->addEmergency($msg, $context);
    }
}