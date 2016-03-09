<?php
/**
 * @author Calmacil <thomas.lenoel@gmail.com>
 * @package \Mf\Http
 * @copyright Calmacil 2016
 * @licence MIT
 */

namespace Calma\Mf;


class PdoProvider
{
    const DRIVER_MYSQL = 'mysql';
    const DRIVER_SQLITE = 'sqlite';

    /**
     * @var array-of-PdoProvider
     */
    protected static $instances;

    /**
     * @var \PDO
     */
    private $dbh;

    /**
     * @var string The data source name
     */
    private $dsn;

    /**
     * @var string The DB driver
     */
    private $driver = self::DRIVER_MYSQL;

    /**
     * @var string the hostname or IP address
     */
    private $host;

    /**
     * @var int
     */
    private $port = 3306;

    /**
     * @var string
     */
    private $dbname;

    /**
     * @var string
     */
    private $user;

    /**
     * @var string
     */
    private $password;

    /**
     * PdoProvider constructor.
     *
     * Instantiates the PDO object. Connection data is not assigned, you have to extends PdoProvider and call its
     * constructor in the child class' one.
     *
     * @param string $name
     * @param array $options
     */
    protected function __construct($name, $options = array())
    {
        $this->host = Config::get('db')->{$name}->host ? : null;
        $this->port = Config::get('db')->{$name}->port ? : null;
        $this->dbname = Config::get('db')->{$name}->dbname ? : null;
        $this->user = Config::get('db')->{$name}->user ? : null;
        $this->password = Config::get('db')->{$name}->password ? : null;

        $this->dsn = $this->buildDsn($name);

        $this->dbh = new \PDO($this->dsn, $this->user, $this->password, $options);
    }

    /**
     * @param string $name
     * @return \PDO
     */
    public static function getConnector($name, $options = array())
    {
        if (!isset(self::$instances[$name]))
            self::$instances[$name] = new self($name, $options);
        return self::$instances[$name]->getDbh();
    }

    /**
     * @return \PDO
     */
    public function getDbh()
    {
        return $this->dbh;
    }

    /**
     * @param string $name
     * @return string
     */
    private function buildDsn($name)
    {
        $this->driver = Config::get('db')->{$name}->driver;
        switch ($this->driver) {
            case self::DRIVER_MYSQL:
                return "mysql:host=" . $this->host . ";port=" . $this->port . ";dbname=" . $this->dbname;
            case self::DRIVER_SQLITE:
                return "sqlite:" . $this->dbname;
        }
        return false;
    }
}
