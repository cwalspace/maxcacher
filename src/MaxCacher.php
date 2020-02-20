<?php 
namespace cwalspace\MaxCacher;

class MaxCacher implements \cwalspace\MaxCacher\iCommon
{
    protected $driver;
    protected $config;

    public function __construct(\cwalspace\MaxCacher\Config $config)
    {
        $this->config = $config;

        $driver_class = '\\cwalspace\\MaxCacher\\Driver\\' . ucfirst($this->config->getDriver());
        $this->driver = new $driver_class($this->config);
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
        return $this->driver->del($key, null, time());
    }
}
?>