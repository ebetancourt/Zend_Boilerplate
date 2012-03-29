<?php
/**
 * Eb.
 *
 * @category Eb
 * @package  Eb_Db
 * @author   Elliot Betancourt <elliot@hipnotyx.com>
 */

/**
 * Class that handles connections to the database server.
 *
 * @category Eb
 * @package  Eb_Db
 * @author   Elliot Betancourt <elliot@hipnotyx.com>
 */
class Eb_Db_Connection
{
    /**
     * Returns the primary database connection.
     *
     * @return Zend_Db_Adapter_Abstract The database connection.
     */
    public static function getConnection()
    {
        if (!isset(self::$_primary))
        {
            self::$_primary = self::_getConnection('primary');
        }

        return self::$_primary;
    } // function getConnection()

    /**
     * Returns the connection to the mongo_cache backend database.
     *
     * @return Mongo The mongo_cache connection.
     */
    public static function getMongoCache()
    {
        if(!self::$_mongoCache) {
            self::$_mongoCache = self::_getMongoConnection('mongo_cache');
        }

        return self::$_mongoCache;
    } // function getMongoCache()

    ////////////////////////////////////////////////////////////////////////////
    // private:
    ////////////////////////////////////////////////////////////////////////////

    /**
     * Returns a connection to a named mongo database.
     *
     * @param  string $name The name of the database.
     * @return Mongo The connection.
     */
    private static function _getMongoConnection($name)
    {
        $config = Eb_Application_Config::getConfig()->resources->db;

        if (isset($config->$name))
        {
            // Only one instance available, so choose this one
            $config = $config->$name;
        }

        // Create the connection
        $connection = new Mongo($config->connection_string, array('replicaSet' => true));

        return $connection;
    } // function _getMongoConnection($name)

    /**
     * Returns a connection to a named database.
     *
     * @param  string $name The name of the database.
     * @return Zend_Db_Adapter_Abstract The connection.
     */
    private static function _getConnection($name)
    {
        $config = Eb_Application_Config::getConfig()->resources->db;

        if (!isset($config->$name))
        {
            /*
                TODO replace this with an exception instead of a false
            */
            return false;
        }

        $connection = Zend_Db::factory($config->$name->adapter, array(
                      'host'     => $config->$name->server,
                      'username' => $config->$name->username,
                      'password' => $config->$name->password,
                      'dbname'   => $config->$name->dbname
                      ));

        return $connection;
    } // function _getConnection($name)

    ////////////////////////////////////////////////////////////////////////////

    /**
     * Connection to the primary database.
     *
     * @var Zend_Db_Adapter_Abstract
     */
    private static $_primary = null;
} // class Eb_Db_Connection
