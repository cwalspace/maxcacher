<?php 

namespace cwalspace\MaxCacher\Driver;

class Runtime implements \cwalspace\MaxCacher\iCommon
{
    protected $config;
    private $map;

    public function __construct(\cwalspace\MaxCacher\Config $config)
    {
        $this->config = $config;
    }

    public function set($key, $value, $ttl = null)
    {
        $hash = md5($key);

        if ($ttl === null) {
            $ttl = time() + $this->config->getDefaultTtl();
        } else if ($ttl <= time()) {
            return false;
        }

        $this->map[$hash] = ['val' => $value, 'ttl' => $ttl];

        return true;
    }

    public function get($key)
    {
        $hash = md5($key);

        if (!isset($this->map[$hash])) {
            return null;
        } else if (time() < $this->map[$hash]['ttl']) {
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

        return false;
    }
}
