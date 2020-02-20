<?php 

namespace cwalspace\MaxCacher\Driver;

class Redis
{
    protected $config;
    protected $redis;

    public function __construct(\cwalspace\MaxCacher\Config $config)
    {
        $this->config = $config;
        $this->redis = new \Predis\Client($config->get('redis'), ['prefix' => $config->getPrefix() .':']);
    }

    public function set($key, $value, $ttl = null)
    {
        $hash = md5($key);

        $ttl += (($ttl === null) ? $this->config->getDefaultTtl() : -time());

        return $this->redis->pipeline(function($pipe) use ($hash, $value, $ttl) {
            $pipe->set($hash, $value);
            $pipe->expire($hash, $ttl);
        });
    }

    public function get($key)
    {
        return $this->redis->get(md5($key)) ?: null;
    }

    public function del($key)
    {
        return $this->redis->del(md5($key));
    }
}
