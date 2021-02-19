<?php

namespace Minify\Test;

use Minify_CacheInterface;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    /** @var string */
    protected static string $document_root;
    /** @var string */
    protected static string $test_files;

    public static function setupBeforeClass(): void
    {
        self::$document_root = __DIR__;
        self::$test_files = __DIR__ . '/_test_files';
    }

    /**
     * Get number of bytes in a string regardless of mbstring.func_overload
     *
     * @param string $str
     * @return int
     */
    protected function countBytes(string $str): int
    {
        return (function_exists('mb_strlen'))
            ? mb_strlen($str, '8bit')
            : strlen($str);
    }

    /**
     * Common assertion for cache tests.
     *
     * @param Minify_CacheInterface $cache
     * @param string $id
     * @param string $data
     */
    protected function assertTestCache(Minify_CacheInterface $cache, string $id, string $data): void
    {
        self::assertTrue($cache->store($id, $data), "$id store");
        self::assertEquals($cache->getSize($id), $this->countBytes($data), "$id getSize");
        self::assertTrue($cache->isValid($id, $_SERVER['REQUEST_TIME'] - 10), "$id isValid");

        ob_start();
        $cache->display($id);
        $displayed = ob_get_clean();

        self::assertSame($data, $displayed, "$id display");
        self::assertEquals($data, $cache->fetch($id), "$id fetch");
    }

    /**
     * Read data file, assert that it exists and is not empty.
     * As a side effect calls trim() to fight against different Editors that insert or strip final newline.
     *
     * @param string $filename
     * @return string
     */
    protected function getDataFile(string $filename): string
    {
        $path = self::$test_files . '/' . $filename;
        self::assertFileExists($path);
        $contents = file_get_contents($path);
        self::assertNotEmpty($contents);
        $contents = trim($contents);
        self::assertNotEmpty($contents);

        return $contents;
    }
}
