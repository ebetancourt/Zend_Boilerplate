<?php
/**
 * Eb.
 *
 * @category Eb
 * @package  Eb
 * @author   Elliot Betancourt
 */

/**
 * Singleton for direct use of memcache, coded to use two sets of memcache servers for redundancy.
 *
 * @category Eb
 * @package  Eb
 * @author   Elliot Betancourt
 */
class Eb_Memcache
{
    /**
     * Constructor for this class
     *
     * @param boolean $isSecondary (Optional) Flag telling the constructor to return the secondary connection rather than the primary
     */
    protected function __construct($isSecondary = false)
    {
        $this->_memcache = new Memcache;
        $config          = new Zend_Config_Xml(CONFIG_DIRECTORY . '/session/backend.xml');

        if ($isSecondary)
        {
            $servers = $config->secondarySessionStore->servers;
        }
        else
        {
            $servers = $config->primarySessionStore->servers;
        }

        foreach ($servers as $server)
        {
            $this->_memcache->addServer($server->host, $server->port, true, $server->weight);
        }

        return $this;
    } // function __construct($servers)

    ///////////////////////////////////////////////////////////////////////////

    /**
     * Set the value in memcache if the value does not exist; returns FALSE if value exists
     *
     * @param  string  $key      Cache key to save
     * @param  mixed   $var      Variable to save into cache
     * @param  boolean $compress (Optional) Whether or not to compress this item
     * @param  integer $expire   (Optional) Seconds before item expires
     * @return boolean
     */
    static function add($key, $var, $compress = false, $expire = 0)
    {
        $primaryResult   = self::primary()->_memcache->add($key, $var, $compress, $expire);
        $secondaryResult = self::secondary()->_memcache->add($key, $var, $compress, $expire);

        return (boolean) ($primaryResult && $secondaryResult);
    }

    ///////////////////////////////////////////////////////////////////////////

    /**
     * Decrement an existing value
     *
     * @param  string $key   Cache key to increment
     * @param  mixed  $value (Optional) Amount by which to decrement value
     * @return boolean
     */
    static function decrement($key, $value = 1)
    {
        $primaryResult   = self::primary()->_memcache->decrement($key, $value);
        $secondaryResult = self::secondary()->_memcache->decrement($key, $value);

        return (boolean) ($primaryResult && $secondaryResult);
    }
    ///////////////////////////////////////////////////////////////////////////

    /**
     * Delete a record or set a timeout
     *
     * @param  string  $key     Cache key for the item in question
     * @param  integer $timeout (Optional) Seconds before the cache gets deleted
     * @return boolean
     */
    static function delete($key, $timeout = 0)
    {
        $primaryResult   = self::primary()->_memcache->delete($key, $timeout);
        $secondaryResult = self::secondary()->_memcache->delete($key, $timeout);

        return (boolean) ($primaryResult && $secondaryResult);
    }

    ///////////////////////////////////////////////////////////////////////////

    /**
     * Clear the cache
     *
     * @return void
     */
    static function flush()
    {
        self::primary()->flush();
        self::secondary()->flush();
    }

    ///////////////////////////////////////////////////////////////////////////

    /**
     * Returns the value stored in the memory by it's key
     *
     * @param  string $key Cache key to retrieve
     * @return mixed
     */
    static function get($key)
    {
        if ($result = self::primary()->_memcache->get($key))
        {
            return $result;
        }
        else if ($result = self::secondary()->_memcache->get($key))
        {
            return $result;
        }

        return false;
    }

    ///////////////////////////////////////////////////////////////////////////

    /**
     * Increment an existing integer value
     *
     * @param  string $key   Cache key to increment
     * @param  mixed  $value (Optional) Amount by which to increment value
     * @return boolean
     */
    static function increment($key, $value = 1)
    {
        $primaryResult   = self::primary()->_memcache->increment($key, $value);
        $secondaryResult = self::secondary()->_memcache->increment($key, $value);

        return (boolean) ($primaryResult && $secondaryResult);
    }

    ///////////////////////////////////////////////////////////////////////////

    /**
     * Singleton to call from all other functions
     *
     * @return Eb_Memcache
     */
    static function primary()
    {
        if (!isset(self::$primary))
        {
            self::$primary = new Eb_Memcache();
        }

        return self::$primary;
    }

    ///////////////////////////////////////////////////////////////////////////

    /**
     * Replace an existing value
     *
     * @param  string  $key      Cache key to save
     * @param  mixed   $var      Variable to save into cache
     * @param  boolean $compress (Optional) Whether or not to compress this item
     * @param  integer $expire   (Optional) Seconds before item expires
     * @return boolean
     */
    static function replace($key, $var, $compress = false, $expire = 0)
    {
        $primaryResult   = self::primary()->_memcache->replace($key, $var, $compress, $expire);
        $secondaryResult = self::secondary()->_memcache->replace($key, $var, $compress, $expire);

        return (boolean) ($primaryResult && $secondaryResult);
    }

    ///////////////////////////////////////////////////////////////////////////

    /**
     * Singleton to call from all other functions
     *
     * @return Eb_Memcache
     */
    static function secondary()
    {
        if (!isset(self::$secondary))
        {
            self::$secondary = new Eb_Memcache(true);
        }

        return self::$secondary;
    }

    ///////////////////////////////////////////////////////////////////////////

    /**
     * Store the value in the memcache memory (overwrite if key exists)
     *
     * @param  string  $key      Cache key to save
     * @param  mixed   $var      Variable to save into cache
     * @param  boolean $compress (Optional) Whether or not to compress this item
     * @param  integer $expire   (Optional) Seconds before item expires
     * @return boolean
     */
    static function set($key, $var, $compress = false, $expire = 0)
    {
        $primaryResult   = self::primary()->_memcache->set($key, $var, $compress, $expire);
        $secondaryResult = self::secondary()->_memcache->set($key, $var, $compress, $expire);

        return (boolean) ($primaryResult && $secondaryResult);
    }
    
    ///////////////////////////////////////////////////////////////////////////

    /**
     * Cache Key Prefix
     *
     * @var string
     */
    protected static $primary = null;
    
    ///////////////////////////////////////////////////////////////////////////

    /**
     * Cache Key Prefix
     *
     * @var string
     */
    protected static $secondary = null;
} // class Eb_Memcache