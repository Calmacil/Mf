<?php
/**
 * @author Calmacil <thomas.lenoel@gmail.com>
 * @package \Mf\Http
 * @copyright Calmacil 2016
 * @licence MIT
 */

namespace Mf;


use Mf\Http\Request;
use Mf\Http\Response;
use Mf\Routing\Router;
use Monolog\Handler\BrowserConsoleHandler;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class Application
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @var Response
     */
    private $response;

    /**
     * @var string The environment
     */
    private $env;

    /**
     * @var string Main configuration file, name depends on the $env param
     */
    public $cfile;

    /**
     * @var Router
     */
    private $router;

    /**
     * @var Controller
     */
    private $controller;

    /**
     * @var array-of-Logger
     */
    private $loggers = array();

    /**
     * Application constructor. This is the entry point of Calmacil/Mf.
     * @param string $root
     * @param string $env
     */
    public function __construct($root, $env = 'prod')
    {
        define('ROOT', $root);
        $this->env = $env;
        $this->cfile = "settings_" . $this->env;

        Config::init(ROOT . '/config/');

        // Loggers
        $rotateHandler = new RotatingFileHandler(
            ROOT.Config::get($this->cfile)->log->logfile,
            10,
            Config::get($this->cfile)->log->loglevel);

        $this->loggers['core'] = new Logger('core');
        $this->loggers['app'] = new Logger('app');

        $this->loggers['core']->pushHandler($rotateHandler);
        $this->loggers['app']->pushHandler($rotateHandler);
        if (Config::get($this->cfile)->debug) {
            $browserHandler = new BrowserConsoleHandler();
            $this->loggers['core']->pushHandler($browserHandler);
            $this->loggers['app']->pushHandler($browserHandler);
        }

        $this->router = Router::getInstance(ROOT . Config::get($this->cfile)->paths->routing_file, $this);
        $this->request = new Request($this, $_SERVER['REQUEST_URI']);
        $this->response = new Response($this);

        $this->coreLogger()->addNotice("Application initialized.");
    }

    public function run()
    {
        try {
            $this->request->proceed();

            $controller_name = ucfirst($this->request->getController());
            $action_name = $this->request->getAction();

            $class = '\\' . ucfirst(Config::get($this->cfile)->project_name) . '\\' . $controller_name;
            $this->controller = new $class($this);

            if (!method_exists($this->controller, $action_name)) {
                $msg = "Action $action_name does not exist in $controller_name.";
                throw new \Exception($msg);
            }
            if (($content_type = $this->controller->{$action_name}) === false) {
                $msg = "Error executing the action. Good luck.";
                throw new \Exception($msg);
            }

            $this->response->setTemplate($this->controller->getTemplate($action_name));
            $this->response->render($content_type);

        } catch (\Exception $e) {
            $this->coreLogger()->addCritical("Could not run the application correctly.\nReason: {reason}\nTrace: {trace}",
                array('reason'=>$e->getMessage(), 'trace'=> $e->getTraceAsString()));
            header('HTTP/1.1 500 Internal Server Error');
            header('Content-Type: text/html');
            $content = <<<EOT
<!DOCTYPE html>
<html lang="fr">
    <head>
        <title>Internal Server Error</title>
    </head>
    <body>
        <h1>C’est dommage !</h1>
        <p>C’est con mais ça marche pas. Non, pas du tout. Allez, comme t’es gentil, je te mets l’erreur. Pas de bêtises
        la prochaine fois !</p>
        <pre>
        Tu es un pingouin. Allez, va prévenir l’administrateur !
        </pre>
    </body>
</html>
EOT;

            echo $content;
        }
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @return Logger
     */
    public function coreLogger()
    {
        return $this->loggers['core'];
    }

    /**
     * @return Logger
     */
    public function appLogger()
    {
        return $this->loggers['app'];
    }
}