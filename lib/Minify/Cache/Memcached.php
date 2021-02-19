<?php
/**
 * Class Minify_Cache_Memcached
 * @package Minify
 */

/**
 * Memcached-based cache class for Minify
 *
 * <code>
 * // fall back to disk caching if memcached can't connect
 * $memcached = new Memcached;
 * if ($memcached->addServer('localhost', 11211)) {
 *     Minify::setCache(new Minify_Cache_Memcached($memcached));
 * } else {
 *     Minify::setCache();
 * }
 * </code>
 **/
class Minify_Cache_Memcached implements Minify_CacheInterface
{

    /**
     * Create a Minify_Cache_Memcached object, to be passed to
     * Minify::setCache().
     *
     * @param Memcached $memcached already-connected instance
     *
     * @param int $expire seconds until expiration (default = 0
     * meaning the item will not get an expiration date)
     */
    public function __construct(Memcached $memcached, $expire = 0)
    {
        $this->_mc = $memcached;
        $this->_exp = $expire;
    }

    /**
     * Write data to cache.
     *
     * @param string $id cache id
     *
     * @param string $data
     *
     * @return bool success
     */
    public function store(string $id, string $data): bool
    {
        return $this->_mc->set($id, "{$_SERVER['REQUEST_TIME']}|{$data}", $this->_exp);
    }

    /**
     * Get the size of a cache entry
     *
     * @param string $id cache id
     *
     * @return int size in bytes
     */
    public function getSize(string $id): int
    {
        if (!$this->_fetch($id)) {
            return false;
        }

        if (function_exists('mb_strlen')) {
            return mb_strlen($this->_data, '8bit');
        }

        return strlen($this->_data);
    }

    /**
     * Does a valid cache entry exist?
     *
     * @param string $id cache id
     *
     * @param int $srcMtime mtime of the original source file(s)
     *
     * @return bool exists
     */
    public function isValid(string $id, int $srcMtime): bool
    {
        return ($this->_fetch($id) && ($this->_lm >= $srcMtime));
    }

    /**
     * Send the cached content to output
     *
     * @param string $id cache id
     */
    public function display(string $id): void
    {
        echo $this->_fetch($id) ? $this->_data : '';
    }

    /**
     * Fetch the cached content
     *
     * @param string $id cache id
     *
     * @return string
     */
    public function fetch(string $id): string
    {
        return $this->_fetch($id) ? $this->_data : '';
    }

    private $_mc;
    private $_exp;

    // cache of most recently fetched id
    private $_lm;
    private $_data;
    private $_id;

    /**
     * Fetch data and timestamp from memcached, store in instance
     *
     * @param string $id
     *
     * @return bool success
     */
    private function _fetch(string $id): bool
    {
        if ($this->_id === $id) {
            return true;
        }

        $ret = $this->_mc->get($id);
        if (false === $ret) {
            $this->_id = null;

            return false;
        }

        [$this->_lm, $this->_data] = explode('|', $ret, 2);
        $this->_id = $id;

        return true;
    }
}
