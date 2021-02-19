<?php

/**
 * Class Minify_Cache_Null
 *
 * If this is used, Minify will not use a cache and, for each 200 response, will
 * need to recombine files, minify and encode the output.
 *
 * @package Minify
 */
class Minify_Cache_Null implements Minify_CacheInterface
{
    /**
     * Write data to cache.
     *
     * @param string $id cache id (e.g. a filename)
     * @param string $data
     *
     * @return bool success
     */
    public function store(string $id, string $data): bool
    {
    }

    /**
     * Get the size of a cache entry
     *
     * @param string $id cache id (e.g. a filename)
     *
     * @return int size in bytes
     */
    public function getSize(string $id): int
    {
    }

    /**
     * Does a valid cache entry exist?
     *
     * @param string $id cache id (e.g. a filename)
     * @param int $srcMtime mtime of the original source file(s)
     *
     * @return bool exists
     */
    public function isValid(string $id, int $srcMtime): bool
    {
    }

    /**
     * Send the cached content to output
     *
     * @param string $id cache id (e.g. a filename)
     */
    public function display(string $id): void
    {
    }

    /**
     * Fetch the cached content
     *
     * @param string $id cache id (e.g. a filename)
     *
     * @return string
     */
    public function fetch(string $id): string
    {
    }
}
