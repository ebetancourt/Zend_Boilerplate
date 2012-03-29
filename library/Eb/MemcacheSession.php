<?php
/**
 * Eb.
 *
 * @category Eb
 * @package  Eb
 * @author   Elliot Betancourt
 */

/**
 * Singleton for direct use of memcache.
 *
 * @category Eb
 * @package  Eb
 * @author   Elliot Betancourt
 */
class Eb_MemcacheSession extends Eb_Memcache
{
    /**
     * Closes the current session for writes
     *
     * @return boolean
     */
    public static function sessionClose()
    {
        return true;
    }

    ///////////////////////////////////////////////////////////////////////////

    /**
     * Destroys all information in the current session
     *
     * @param  string $sessionKey Name for this session variable
     * @return mixed
     */
    public static function sessionDestroyer($sessionKey)
    {
        return self::delete(self::$cachePrefix . $sessionKey);
    }

    ///////////////////////////////////////////////////////////////////////////

    /**
     * Runs the garbage collector for this session store
     *
     * @param  string $maxlifetime Maximum lifetime for an object in the session in seconds
     * @return mixed
     */
    public static function sessionGc ($maxlifetime)
    {
        // memcache will clean itself out when the sessions expire
        return $true;
    }


    ///////////////////////////////////////////////////////////////////////////

    /**
     * Runs the garbage collector for this session store
     *
     * @param  string $savePath    Where the current session should be saved
     * @param  string $sessionName Name for the session
     * @return boolean
     */
    public static function sessionOpen($savePath, $sessionName)
    {
        // nothing to do, all sessions being saved to memcache
        return true;
    }

    ///////////////////////////////////////////////////////////////////////////

    /**
     * Retrieves a value from the session
     *
     * @param  string $sessionKey Name for this session variable
     * @return mixed
     */
    public static function sessionRead($sessionKey)
    {
        return self::get(self::$cachePrefix . $sessionKey);
    }

    ///////////////////////////////////////////////////////////////////////////

    /**
     * Saves a value to the session
     *
     * @param  string  $sessionKey Name for this session variable
     * @param  integer $value      Value for this session variable
     * @return mixed
     */
    public static function sessionWrite($sessionKey, $value)
    {
        $sessionKey = addslashes($sessionKey);
        $value      = addslashes($value);
        $session    = self::get(self::$cachePrefix . $sessionKey);

        if ($session === false)
        {
            // insert key into memcache for 5 minutes
            return self::add(self::$cachePrefix . $sessionKey, $value, false, 300);
        }
        else
        {
            // update memcache key
            return self::replace(self::$cachePrefix . $sessionKey, $value, false, 300);
        }
    }

    ///////////////////////////////////////////////////////////////////////////

    /**
     * Sets this object as the session handler for php
     *
     * @return void
     */
    public static function setAsSessionHandler()
    {
        session_set_save_handler(array('Eb_MemcacheSession', 'sessionOpen'),
                                 array('Eb_MemcacheSession', 'sessionClose'),
                                 array('Eb_MemcacheSession', 'sessionRead'),
                                 array('Eb_MemcacheSession', 'sessionWrite'),
                                 array('Eb_MemcacheSession', 'sessionDestroyer'),
                                 array('Eb_MemcacheSession', 'sessionGc'));
    }

    ///////////////////////////////////////////////////////////////////////////

    /**
     * Cache Key Prefix
     *
     * @var string
     */
    public static $cachePrefix = 'session_memcache_prefix';
} // class Eb_MemcacheSession