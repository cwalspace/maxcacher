<?php 

namespace cwalspace\MaxCacher\Driver;

define('DS', DIRECTORY_SEPARATOR);

class File
{
    protected   $config;
    private     $path;
    
    public function __construct(\cwalspace\MaxCacher\Config $config)
    {
        $this->config = $config;
        $this->path = rtrim($this->config->getPath(), DS) . DS . $this->config->getPrefix() . DS;
    }

    public function set($key, $value, $ttl = null)
    {     
        $filename = md5($key);

        if ($ttl === null) {
            $ttl = time() + $this->config->getDefaultTtl();
        } else if ($ttl <= time()) {
            return false;
        }

        try {
            return $this->write($filename, $value, $ttl);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function get($key)
    {
        $filename = md5($key);

        try {
            return $this->read($filename);
        } catch (\Exception $e) {
            return null;
        }
    }

    public function del($key) 
    {
        $filename = md5($key);

        $fullPath = $this->path . $filename;

        return @unlink($fullPath);
    }

    private function write($filename, $data, $ttl = null)
    {
        $fullPath   = $this->path . $filename;
        $ret        = false;

        if (!@is_dir($this->path) && !@mkdir($this->path, 0750, true)) {
            throw new \Exception('access error: could\'t create dir / ' . $this->path);
        }

        if (!($fh = @fopen($fullPath, 'wb'))) { 
            throw new \Exception('write error: couldn\'t open file for write / '. $fullPath);
        }

        if ($ttl === null) {
            $ttl = time() + $this->config->getDefaultTtl();
        }

        if (flock($fh, LOCK_EX)) {
            $ret = fwrite($fh, serialize([$ttl, $data]));
            flock($fh, LOCK_UN);
        } else {
            fclose($fh);
            throw new \Exception('access error: couldn\'t obtain exclusive lock / '. $fullPath);
        }

        fclose($fh);

        return $ret;
    }

    private function read($filename)
    {
        $fullPath = $this->path . $filename;

        if (!file_exists($fullPath)) {
            return null;
        }

        if (!($fh = @fopen($fullPath, 'rb'))) {
            throw new \Exception('access error: unable to open file for read / '. $fullPath);
        }

        flock($fh, LOCK_SH);
        $tmp = @unserialize(fread($fh, filesize($fullPath)));
        flock($fh, LOCK_UN);
        fclose($fh);

        if (!is_array($tmp)) {
            throw new \Exception('read error: incompatible value type' . $fullPath);
        }

        list($timestamp, $data) = $tmp;

        if ($timestamp <= time()) {
            return null;
        }

        return $data;
    }

}
