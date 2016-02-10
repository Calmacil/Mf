<?php
/**
 * @author Calmacil <thomas.lenoel@gmail.com>
 * @package \Mf\Http
 * @copyright Calmacil 2016
 * @licence MIT
 */

namespace Mf;


class Config
{
    /**
     * @var string
     */
    private $dir;

    /**
     * @var array
     */
    private static $settings;

    /**
     * @var Config
     */
    private static $instance;

    /**
     * Config constructor.
     * @param string $file
     */
    public function __construct($dir)
    {
        $this->dir = $dir;
    }

    public static function init($dir) {
        self::$instance = new Config($dir);
    }

    /**
     * @throws \ErrorException
     * @var string $scope
     * @return bool
     */
    public function load($scope)
    {
        $file = file_get_contents($this->dir . '/' . $scope . '.json');
        if (!($file)) {
            throw new \ErrorException("Impossible to read config file");
        }

        self::$settings[$scope] = json_decode($file);

        if (!self::$settings[$scope]) {
            $e = json_last_error_msg();
            throw new \ErrorException("Impossible to load config file:\n$e");
        }
        return true;
    }

    /**
     * @param string $key
     * @return mixed
     */
    public static function get($scope)
    {
        if (!isset(self::$settings[$scope])) {
            self::$instance->load($scope);
        }
        return self::$settings[$scope];
    }
}