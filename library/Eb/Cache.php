<?php
class Eb_Cache
{
    private static $instance = null;

    public static function getInstance()
    {
        if (empty(self::$instance))
        {
            $memcacheServers = Eb_Application_Config::getConfig()->resources->memcache;
            $memcached       = new Memcached();

            foreach ($memcacheServers as $server)
            {
                $memcached->addServer($server['host'], $server['port']);
            }

            self::$instance = $memcached;
        }

        return self::$instance;
    }

    private function __construct()
    {
    }

    public function __clone()
    {
        trigger_error('Clone is not allowed.', E_USER_ERROR);
    }

    public function __wakeup()
    {
        trigger_error('Unserializing is not allowed.', E_USER_ERROR);
    }
}