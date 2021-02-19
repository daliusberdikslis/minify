<?php

namespace Minify\Test;

use Memcache;
use Minify_Cache_Memcached;

class MinifyCacheMemcachedTest extends TestCase
{
    /** @var Memcached */
    private Memcached $mc;

    public function setUp():void
    {
        if (!function_exists('addServer')) {
            self::markTestSkipped("To test this component, install memcached in PHP");
        }

        $this->mc = new Memcached();
        if (!$this->mc->addServer('localhost', 11211)) {
            self::markTestSkipped("Memcached server not found on localhost:11211");
        }
    }

    public function test1(): void
    {
        $data = str_repeat(md5(time()) . 'í', 100); // 3400 bytes in UTF-8
        $id = 'Minify_test_memcache';
        $cache = new Minify_Cache_Memcache($this->mc);

        $this->assertTestCache($cache, $id, $data);
    }

    public function test2(): void
    {
        if (!function_exists('gzencode')) {
            self::markTestSkipped("enable gzip extension to test this");
        }

        $data = str_repeat(md5(time()) . 'í', 100); // 3400 bytes in UTF-8
        $id = 'Minify_test_memcache.gz';
        $cache = new Minify_Cache_Memcache($this->mc);

        $data = gzencode($data);
        $this->assertTestCache($cache, $id, $data);
    }
}
