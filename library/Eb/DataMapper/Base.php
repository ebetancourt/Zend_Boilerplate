<?php
class Eb_DataMapper_Base
{
    protected $_defaultCacheTime = 300; // 5 minutes
    protected $_cacheTimes       = array();

    public function getCacheTime($methodName)
    {
        return (isset($this->_cacheTimes[$methodName])) ? $this->_cacheTimes[$methodName] : $this->_defaultCacheTime;
    }
    
    public function __construct()
    {
        $this->_db = Db::getInstance();
        $this->init();
    }

    protected function init()
    {
    }
}