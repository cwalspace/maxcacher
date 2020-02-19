<?php 

namespace Tests\Driver;

use PHPUnit\Framework\TestCase;

class RedisTest extends TestCase
{
    protected $adapter;
    protected $config;

    public function setUp(): void
    {
        $redis = [
            'driver'        => 'redis', 
            'path'          => __DIR__ .'/../', 
            'default_ttl'   => 60 * 60 * 12,
            'redis'         => '127.0.0.1:6379',
            'redis_prefix'  => 'mc'
        ];

        $this->config = new \cwalspace\MaxCacher\Config($redis);

        $this->adapter = new \cwalspace\MaxCacher\MaxCacher($this->config);
    }

    public function testFileSet()
    {
        $value = microtime();
        $this->adapter->set('set', $value);
        $this->assertEquals($value, $this->adapter->get('set'));
    }

    public function testFileDue()
    {
        $value = microtime();
        $this->adapter->set('exp', $value, time());
        $this->assertNull($this->adapter->get('exp'));
    }

    public function testFileDel()
    {
        $value = microtime();
        $this->adapter->set('del', $value);
        $this->adapter->del('del');
        $this->assertNull($this->adapter->get('del'));
    }

}
