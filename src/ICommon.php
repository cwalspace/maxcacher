<?php 
namespace cwalspace\MaxCacher;

interface iCommon
{    
    public function set($key, $value, $ttl = null);
    
    public function get($key);

    public function del($key);
}

?>