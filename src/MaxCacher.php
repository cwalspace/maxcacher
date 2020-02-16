<?php 
namespace cwalspace\MaxCacher;

use cwalspace\MaxCacher\Config;
use cwalspace\MaxCacher\Driver\File;


class MaxCacher
{
    protected $driver;
    protected $config;


    public function __construct(Config $config)
    {
        $this->config = $config;

        $driver_class = '\cwalspace\MaxCacher\Driver\\' . ucfirst($config->driver);
        $this->driver = new $driver_class($config);
    }
    
    public function set($key, $value, $ttl = null)
    {
        return $this->driver->set($key, $value, $ttl);
    }
    
    public function get($key)
    {
        return $this->driver->get($key);
    }

    public function del($key)
    {
        return $this->driver->set($key, null, time());
    }
}
?>