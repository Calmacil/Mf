<?php
/**
 * @author Calmacil <thomas.lenoel@gmail.com>
 * @package \Mf\Core
 * @copyright Calmacil 2016
 * @licence MIT
 */

namespace Mf;


class Config
{
    /**
     * @var string
     */
    private $file;

    /**
     * @var array
     */
    private static $settings;

    /**
     * Config constructor.
     * @param string $file
     */
    public function __construct($file)
    {
        $this->file = $file;
        $this->load();
    }

    /**
     * @throws \ErrorException
     * @return bool
     */
    private function load()
    {
        $file = file_get_contents($this->file);
        if (!($file)) {
            throw new \ErrorException("Impossible to read config file");
        }

        self::$settings = json_decode($file);

        if (!self::$settings) {
            $e = json_last_error_msg();
            throw new \ErrorException("Impossible to load config file:\n$e");
        }
        return true;
    }

    /**
     * @param string $key
     * @return mixed
     */
    public static function get($key=null)
    {
        if ($key)
            return self::$settings[$key];
        return self::$settings;
    }
}