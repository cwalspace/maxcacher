<?php 

namespace cwalspace\MaxCacher;

class Config
{
    private $config = ['driver' => 'runtime', 'default_ttl' => 3600, 'path' => __DIR__, 'prefix' => 'rs'];
    public  $driver = null;
    
    public function __construct($config)
    {
        $this->config = array_merge($this->config, $config);
    }

    public function set($key, $value = null)
    {
        $this->config[$key] = $value;
    }

    public function getDefaultTtl()
    {
        return isset($this->config['default_ttl']) ? $this->config['default_ttl'] : 0;
    }

    public function getPath()
    {
        return isset($this->config['path']) ? $this->config['path'] : __DIR__;
    }

    public function getDriver() 
    {
        return isset($this->config['driver']) ? $this->config['driver'] : null;
    }

    public function getPrefix() 
    {
        return isset($this->config['prefix']) ? $this->config['prefix'] : '';
    }

    public function get($key)
    {
        return isset($this->config[$key]) ? $this->config[$key] : null;
    }
}
