<?php

namespace Tests;

use cwalspace\MaxCacher\MaxCacher;
use cwalspace\MaxCacher\Config;
use cwalspace\MaxCacher\Driver\File;
use cwalspace\MaxCacher\Driver\Redis;
use cwalspace\MaxCacher\Driver\Runtime;
use PHPUnit\Framework\TestCase;

class MaxCacherTest extends TestCase
{
    protected $adapter;
    protected $config;

    public function setUp(): void
    {
        $file = [
            'driver'        => 'file', 
            'path'          => __DIR__ .'/../', 
            'default_ttl'   => 60 * 60 * 12
        ];

        $redis = [
            'driver'        => 'redis', 
            'path'          => __DIR__ .'/../', 
            'default_ttl'   => 60 * 60 * 12,
            'redis'         => '127.0.0.1:6379',
            'redis_prefix'  => 'mc'
        ];

        $runtime = [
            'driver'        => 'runtime',
            'path'          => __DIR__ .'/../', 
            'default_ttl'   => 60 * 60 * 12
        ];

        $this->config = new Config($runtime);

        $this->adapter = new MaxCacher($this->config);
    }

    public function testSet()
    {
        $value = microtime();
        $this->adapter->set('set', $value);
        $this->assertEquals($value, $this->adapter->get('set'));
    }

    public function testDue()
    {
        $value = microtime();
        $this->adapter->set('exp', $value, time());
        $this->assertNull($this->adapter->get('exp'));
    }

    public function testDel()
    {
        $value = microtime();
        $this->adapter->set('del', $value);
        $this->adapter->del('del');
        $this->assertNull($this->adapter->get('del'));
    }

}
