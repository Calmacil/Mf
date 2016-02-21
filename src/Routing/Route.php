<?php
/**
 * @author calmacil <thomas.lenoel@gmail.com>
 * @package \Mf\Http
 * @copyright Calmacil 2016
 * @licence MIT
 */

namespace Calma\Mf\Routing;


use Psr\Log\InvalidArgumentException;

class Route
{
    /**
     * @var string
     */
    private $controller;

    /**
     * @var string
     */
    private $name;

    /**
     * @var array-of-mixed
     */
    private $params = array();

    /**
     * @var string
     */
    private $action = "exec";

    /**
     * @var string
     */
    private $pattern;

    /**
     * @var array-of-mixed
     */
    private $httpParams = array();

    /**
     * Route constructor.
     * @param string $name                  The name of the route
     * @param array-of-string $settings     The settings given from routes.json
     * @throws \ErrorException              If given $settings does not contain 'pattern' or 'controller' keys. Other
     *                                      keys are optional.
     */
    public function __construct($name, $settings)
    {
        if (!array_key_exists('pattern', $settings) || !array_key_exists('controller', $settings)) {
            throw new \ErrorException("Route $name needs at least 'controller' and 'pattern' keys");
        }

        $this->name = $name;
        $this->pattern = $settings->pattern;
        $this->controller = $settings->controller;
        if (isset($settings->param))
            $this->params = $settings->params;
        if (isset($settings->action))
            $this->action = $settings->action;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @param $key
     * @return array
     */
    public function getHttpParams()
    {
        return $this->httpParams;
    }

    /**
     * Generates a URL for the Route's name with the given params
     * @todo add security checks for missing params
     *
     * @param array $params
     * @return string
     */
    public function generate($params)
    {
        $uri = $this->pattern;

        if ($params && is_array($params)) {
            foreach (array_keys($this->params) as $key) {
                $uri = str_replace(":$key:", $params[$key], $uri);
            }
        }
        return $uri;
    }

    /**
     * Checks $url against the Route's pattern and extracts it's params value
     * @param string $url
     * @return bool
     */
    public function check($url)
    {
        $formatted_regex = $this->pattern;
        foreach ($this->params as $param => $format) {
            $formatted_regex = str_replace(":$param:", $url, $formatted_regex);
        }

        if (!preg_match("#$formatted_regex#", $url, $matches)) {
            return false;
        }

        $par_pos = $this->computeParamPosition();

        foreach (array_keys($this->params) as $param) {
            $this->httpParams[$param] = $matches[array_search($param, $par_pos)];
        }
        return true;
    }

    /**
     * Computes params position in pattern
     * @return array
     */
    private function computeParamPosition()
    {
        preg_match("#:". implode(':|:', $this->params) . "#", $this->pattern, $matches);
        return $matches;
    }
}