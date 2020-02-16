<?php 

namespace cwalspace\MaxCacher\Driver;

use cwalspace\MaxCacher\Config;

class Runtime
{
    protected $config;
    private $map;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function set($key, $value, $ttl)
    {
        if ($ttl <= time() && $ttl !== null) {
            unset($this->map[md5($key)]);
        } 

        if ($ttl === null) {
            $ttl = time() + $this->config->default_ttl;
        }

        $this->map[md5($key)] = ['val' => $value, 'ttl' => $ttl];

        return true;
    }

    public function get($key)
    {
        if (time() < $this->map[md5($key)]['ttl']) {
            return $this->map[md5($key)]['val'];
        } else {
            unset($this->map[md5($key)]);
            return null;
        }
    }
}
