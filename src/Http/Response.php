<?php
/**
 * @author Calmacil <thomas.lenoel@gmail.com>
 * @package \Mf\Http
 * @copyright Calmacil 2016
 * @licence MIT
 */

namespace Mf\Http;


use Mf\Config;
use Mf\Routing\Router;

class Response
{
    const STATUS_OK = "200 OK";
    const STATUS_FOUND = "302 Found";
    const STATUS_NOT_FOUND = "404 Not Found";

    const TYPE_HTML = "text/html";
    const TYPE_JSON = "text/json";

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
     */
    public function __construct()
    {
        $PATHS = Config::get("path");

        $this->loader = new \Twig_Loader_Filesystem(ROOT.$PATHS->templates);
        $this->environment = new \Twig_Environment($this->loader, array(
            "cache" => ROOT.$PATHS->twig_cache,
            "debug" => Config::get('debug')
        ));

        // register custom functions
        $function_names = get_class_methods("\\Mf\\Twig\\Functions");
        foreach ($function_names as $func_name) {
            $func = new \Twig_SimpleFunction($func_name, array("\\Mf\\Twig\\Functions", $func_name));
            $this->environment->addFunction($func);
        }
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
    }

    /**
     * Renders output as HTML
     */
    public function render($content_type = self::TYPE_HTML)
    {
        if (Config::get('debug')) {
            // TODO: implements debugbar here
        }

        $content = $this->environment->render($this->template, $this->template_vars);

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

        ob_clean();
        ob_start();
        header("HTTP/1.1 " . self::STATUS_FOUND);
        header("Location : " . $url);
        ob_end_flush();
        return true;
    }
}