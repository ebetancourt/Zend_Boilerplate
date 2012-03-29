<?php
/**
 * Eb.
 *
 * @category Eb
 * @package  Eb_Application
 * @author   Elliot Betancourt <elliot@hipnotyx.com>
 */

/**
 * Class that handles saving and retrieving the application configuration.
 *
 * @category Eb
 * @package  Eb_Application
 * @author   Elliot Betancourt <elliot@hipnotyx.com>
 */
class Eb_Application_Config
{
    /**
     * Returns the primary application configuration.
     *
     * @return Zend_Config_Ini The application configuration.
     */
    public static function getConfig()
    {
        if (!isset(self::$_primary))
        {
            self::$_primary = self::_getConfig('application');
        }

        return self::$_primary;
    } // function getConnection()

    /**
     * Returns the eb application configuration.
     *
     * @return Zend_Config_Ini The application configuration.
     */
    public static function getEb()
    {
        if (!isset(self::$_eb))
        {
            self::$_eb = self::_getConfig('eb');
        }

        return self::$_eb;
    } // function getConnection()

    ////////////////////////////////////////////////////////////////////////////
    // private:
    ////////////////////////////////////////////////////////////////////////////

    /**
     * Returns a named set of configuration settings.
     *
     * @param  string $name The name of the configuration.
     * @return Zend_Config_Ini The configuration.
     */
    private static function _getConfig($name)
    {
        $config = new Zend_Config_Ini(EB_CONFIG_PATH . DIRECTORY_SEPARATOR . $name . '.ini', APPLICATION_ENV);

        return $config;
    } // function _getConfig($name)

    ////////////////////////////////////////////////////////////////////////////

    /**
     *  The primary application configuration.
     *
     * @var Zend_Config_Ini
     */
    private static $_primary = null;

    /**
     *  The custom Eb framework configuration.
     *
     * @var Zend_Config_Ini
     */
    private static $_eb = null;
} // class Eb_Application_Config
