<?php
class Eb_DataMapper
{
    private static $instances = array();

    public static function factory($className)
    {
        $className = ucFirst($className);

        if (!isset(self::$instances[$className]))
        {
            $wrappedClass = 'Eb_DataMapper_' . $className;
            $dynamicClassName = __CLASS__;
            $dynamicClass = new $dynamicClassName;
            $dynamicClass->setDataMapper(new $wrappedClass());
            $dynamicClass->setClassName($wrappedClass);
            self::$instances[$className] = $dynamicClass;
        }

        return self::$instances[$className];
    }
    ////////////////////////////////////////////////////////////////////////////

    /**
     * Main method: call the specified method or get the result from cache.
     *
     * @param  string $name       Method name.
     * @param  array  $parameters Method parameters.
     * @return mixed Result
     */
    public function __call($name, $parameters)
    {
        $cache    = Eb_Cache::getInstance();
        $cacheKey = $this->_generateKey($name, $parameters);

        $returnValue = $cache->get($cacheKey);

        if ($cache->getResultCode() != Memcached::RES_SUCCESS)
        {
            // echo "Not Cached $cacheKey<br />";
            $returnValue = call_user_func_array(array($this->_datamapper, $name), $parameters);
            $cacheTime   = $this->_datamapper->getCacheTime($name);
            $cache->set($cacheKey, $returnValue, $cacheTime);
        } else {
            // echo "Cached $cacheKey<br />";
        }

        return $returnValue;
    }

    private function __construct()
    {
    }

    public function setDataMapper($dataMapper)
    {
        $this->_datamapper = $dataMapper;
    }

    public function setClassName($className)
    {
        $this->_className = $className;
    }

    private function _generateKey($method, $parameters)
    {
        $keyPrefix     = $this->_className . '__' . $method . '_';
        $keyParameters = join($parameters, '_');
        return $keyPrefix . md5($keyParameters);
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