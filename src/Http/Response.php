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

class Response
{
    const STATUS_OK = "200 OK";
    const STATUS_FOUND = "302 Found";
    const STATUS_NOT_FOUND = "404 Not Found";

    const TYPE_HTML = "text/html";
    const TYPE_JSON = "text/json";

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
        $function_names = get_class_methods("\\Calma\\Mf\\Twig\\Functions");
        foreach ($function_names as $func_name) {
            $func = new \Twig_SimpleFunction($func_name, array("\\Mf\\Twig\\Functions", $func_name));
            $this->environment->addFunction($func);
        }
        $this->app->coreLogger()->notice('Response initialized.');
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
        header("Location : " . $url);
        ob_end_flush();
        return true;
    }

    public function display404($template = null)
    {
        if ($template) {
            $content = $this->environment->render($template);
        } else {
            $content = <<<EOC
<!DOCTYPE html>
<html>
    <head>
        <meta name="Content-Type" content="text/html"/>
        <meta charset="utf-8"/>
        <title>Page not found!</title>
    </head>
    <body>
        <p>This is not the page you are looking for!</p>
    </body>
</html>
EOC;
            ob_clean();
            ob_start();
            header("HTTP/1.1 " . self::STATUS_NOT_FOUND);
            echo $content;
            ob_end_flush();
            return true;
        }
    }
}