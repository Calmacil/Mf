<?php
/**
 * @author Calmacil <thomas.lenoel@gmail.com>
 * @package \Mf\Http
 * @copyright Calmacil 2016
 * @licence MIT
 */

namespace Calma\Mf\Http;


use Calma\Mf\Application;
use Calma\Mf\Config;
use Calma\Mf\Routing\Router;
use Calma\Mf\Twig\CoreTwigExtension;

class Response
{
    const STATUS_OK = "200 OK";
    const STATUS_FOUND = "302 Found";
    const STATUS_FORBIDDEN = "403 Forbidden";
    const STATUS_NOT_FOUND = "404 Not Found";

    const TYPE_HTML = "text/html";
    const TYPE_JSON = "text/json";

    const TYPE_EXIT = "exit";

    /**
     * @var Application
     */
    private $app;

    /**
     * @var \Twig_Loader_Filesystem
     */
    private $loader;

    /**
     * @var \Twig_Environment
     */
    private $environment;

    /**
     * The template to render
     * @var string
     */
    private $template;

    /**
     * The variables to be rendered on the template
     * @var array-of-mixed
     */
    private $template_vars = array();

    private $status = self::STATUS_OK;

    /**
     * Response constructor. Inits the TWIG environment.
     * @param Application $app
     */
    public function __construct($app)
    {
        $this->app = $app;
        $PATHS = Config::get($app->cfile)->paths;

        $this->loader = new \Twig_Loader_Filesystem(ROOT.$PATHS->templates_dir);
        $this->environment = new \Twig_Environment($this->loader, array(
            "cache" => ROOT.$PATHS->twig_cache,
            "debug" => Config::get($app->cfile)->debug
        ));

        // register custom functions
        $this->environment->addExtension(new CoreTwigExtension());

        $this->environment->addGlobal('_APP_', $this->app); //thus you can use app and plugins functions

        $this->app->coreLogger()->notice('Response initialized.');
    }

    /**
     * Registers a function repository
     *
     * @param string $classname
     * @throws \ErrorException
     */
    public function registerFunctions($classname)
    {
        if (!class_exists($classname)) {
            throw new \ErrorException("Required class does not exist");
        }

        $function_names = get_class_methods($classname);
        foreach ($function_names as $function_name) {
            $func = new \Twig_SimpleFunction($function_name,
                array($classname, $function_name),
                array('needs_environment' => true));
            $this->environment->addFunction($func);
        }
    }

    /**
     * Registers a filters repository
     *
     * @param string $classname
     * @throws \ErrorException
     */
    public function registerFilter($classname)
    {
        if (!class_exists($classname)) {
            throw new \ErrorException("Required class does not exist.");
        }

        $filter_names = get_class_methods($classname);
        foreach($filter_names as $filter_name) {
            $filter = new \Twig_SimpleFilter($filter_name,
                array($classname, $filter_name),
                array('needs_environment' => true));
            $this->environment->addFilter($filter);
        }
    }

    public function registerExtension($extension)
    {
        $this->environment->addExtension($extension);
    }

    /**
     * @param string $template
     */
    public function setTemplate($template)
    {
        $this->template = $template.'.twig';
    }

    /**
     * @param string $key
     * @param mixed $value
     */
    public function assign($key, $value)
    {
        if (is_string($value)) {
            $this->template_vars[$key] = html_entity_decode($value);
        } else {
            $this->template_vars[$key] = $value;
        }
        $this->app->coreLogger()->info("Assigning value {val} to key {key} for display.",
            ['key'=>$key, 'val'=>print_r($value, true)]);
    }

    /**
     * Renders output as HTML
     */
    public function render($content_type = self::TYPE_HTML)
    {
        $this->environment->addGlobal('_current_route', $this->app->getRequest()->getRoute()->getName());
        $content = $this->environment->render($this->template, $this->template_vars);

        $this->app->coreLogger()->notice("Rendering template {tpl} as {ctype} with code: [{code}]", [
            'tpl' => $this->template,
            'ctype' => $content_type,
            'code' => $this->status]);

        ob_clean();
        ob_start();
        header("HTTP/1.1 " . $this->status);
        header("Content-Type: " . $content_type);
        echo $content;
        ob_end_flush();
    }

    /**
     * Redirects the request to the given route. Internal redirect only.
     *
     * @param string $route
     * @param array $options
     * @return bool
     */
    public function redirect($route, $options = array())
    {
        $url = Router::getInstance()->generateRoute($route, $options);

        $this->app->coreLogger()->notice("Redirecting current request to route {route}", ['route' => $route]);

        ob_clean();
        ob_start();
        header("HTTP/1.1 " . self::STATUS_FOUND);
        header("Location: " . $url);
        ob_end_flush();
        return self::TYPE_EXIT;
    }

    /**
     * Displays the 404 page. User should write a 404 template and set it in the config files.
     *
     * @param string $template
     * @param string $ctype
     * @return bool
     */
    public function display404($template = null, $c_type=self::TYPE_HTML)
    {
        if ($template) {
            $content = $this->environment->render($template);
        }elseif (isset(Config::get($this->app->cfile)->page404) && ($tpl = Config::get($this->app->cfile)->page404)) {
            $content = $this->environment->render($tpl);
        } else {
            $content = <<<EOC
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8"/>
        <title>Page not found!</title>
    </head>
    <body>
        <p>This is not the page you are looking for!</p>
    </body>
</html>
EOC;
        }

        ob_clean();
        ob_start();
        header("HTTP/1.1 " . self::STATUS_NOT_FOUND);
        header("Content-Type: " . $c_type);
        echo $content;
        ob_end_flush();

        $this->app->end();
        exit;
    }


    /**
     * Displays the 403 page. User should write a 403 template and set it in the config file.
     *
     * @param string  $template
     * @param string $c_type
     * @return bool
     */
    public function display403($template = null, $c_type = self::TYPE_HTML)
    {
        if ($template) {
            $content = $this->environment-$this->render($template);
        } elseif (isset(Config::get($this->app->cfile)->page403) && ($tpl = Config::get($this->app->cfile)->page403)) {
            $content = $this->environment->render($tpl);
        } else {
            $content = <<<EOC
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8"/>
        <title>Forbidden area</title>
    </head>
    <body>
        <p>You shall not pass!</p>
    </body>
</html>
EOC;
        }

        ob_clean();
        ob_start();
        header("HTTP/1.1 . " . self::STATUS_FORBIDDEN);
        header("Content-Type: " . $c_type);
        echo $content;
        ob_end_flush();

        $this->app->end();
        exit;
    }
}