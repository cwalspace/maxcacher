<?php 

namespace cwalspace\MaxCacher\Driver;

use cwalspace\MaxCacher\Config;

class File
{
    protected $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function set($key, $value, $ttl = null)
    {
        $filename = md5($key);

        if ($ttl <= time() && $ttl !== null) {
            $cache = $this->config->path;
            $path = rtrim($cache, '/') .'/';
            
            return @unlink($path . $filename);
        }

        return $this->write($filename, $value, $ttl);
    }

    public function get($key)
    {
        $filename = md5($key);

        return $this->read($filename);
    }

    private function write($filename, $data, $ttl = null)
    {
        $cache = $this->config->path;
        $path = rtrim($cache, '/') .'/';

        if (!is_dir($path)) {
            throw new \Exception('access error: '. $path);
        }

        if (!($fh = @fopen($path . $filename, 'wb'))) { 
            throw new \Exception('write error: '. $path . $filename);
        }

        if ($ttl === null) {
            $ttl = time() + $this->config->default_ttl;
        }

        flock($fh, LOCK_EX);
        fwrite($fh, $ttl . $data);
        flock($fh, LOCK_UN);
        fclose($fh);

        return true;
    }

    private function read($filename)
    {
        $cache = $this->config->path;
        $path = rtrim($cache, '/') .'/';

        if (!file_exists($path . $filename)) { 
            return null;
        }

        if (!($fh = @fopen($path . $filename, 'rb'))) {
            throw new \Exception('access error: '. $path);
        }

        flock($fh, LOCK_SH);
        $ts = fread($fh, 10);

        if ($ts <= time()) {
            return null;
        }

        $data = fread($fh, filesize($path) - 10);
        flock($fh, LOCK_UN);
        fclose($fh);

        return $data;
    }

}
