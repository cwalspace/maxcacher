<?php 

namespace cwalspace\MaxCacher;

class Config
{
    private $config = [];
    
    public function __construct($config)
    {
        $this->config = $config;
    }

    public function set($key, $value = null)
    {
        $this->config[$key] = $value;
    }

    public function get($key)
    {
        return isset($this->config[$key]) ? $this->config[$key] : null;
    }
}
