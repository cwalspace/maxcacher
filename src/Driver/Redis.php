<?php 

namespace cwalspace\MaxCacher\Driver;

use cwalspace\MaxCacher\Config;

class Redis
{
    protected $config;
    protected $redis;

    public function __construct(Config $config)
    {
        $this->config = $config;
        $this->redis = new \Predis\Client($config->redis, ['prefix' => $config->redis_prefix .'.']);
    }

    public function set($key, $value, $ttl)
    {
        if ($ttl === null) {
            $ttl = $this->config->default_ttl;
        } else {
            $ttl -= time();
        }

        $this->redis->pipeline(function($pipe) use ($key, $value, $ttl) {
            $pipe->set(md5($key), $value);
            $pipe->expire(md5($key), $ttl);
        });
    }

    public function get($key)
    {
        return $this->redis->get(md5($key)) ?: null;
    }
}
