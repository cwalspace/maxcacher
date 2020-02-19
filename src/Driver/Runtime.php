<?php 

namespace cwalspace\MaxCacher\Driver;

class Runtime
{
    protected $config;
    private $map;

    public function __construct(\cwalspace\MaxCacher\Config $config)
    {
        $this->config = $config;
    }

    public function set($key, $value, $ttl = null)
    {
        if ($ttl === null) {
            $ttl = time() + $this->config->default_ttl;
        }

        if ($ttl <= time()) {
            throw new \Exception("error: past TTL");
        }

        $this->map[md5($key)] = ['val' => $value, 'ttl' => $ttl];
        return true;
    }

    public function get($key)
    {
        $hash = md5($key);

        if (!isset($this->map[$hash])) {
            return null;
        }

        if (time() < $this->map[$hash]['ttl']) {
            return $this->map[$hash]['val'];
        }

        unset($this->map[$hash]);
        return null;
    }

    public function del($key)
    {
        $hash = md5($key);

        if (isset($this->map[$hash])) {
            unset($this->map[$hash]);
            return true;
        } 

        throw new Exception('error: key was not found');
    }
}
