<?php
class Eb_Log
{

    protected static $_instance;
    protected $_logger;

    protected function __construct()
    {
        $writer = new Zend_Log_Writer_Firebug();
        $this->_logger = new Zend_Log($writer);
    }

    public function log($msg, $level = Zend_Log::INFO)
    {
        $this->_logger->log($msg, $level);
    }

    public static function getInstance()
    {
        if (self::$_instance === null) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

}