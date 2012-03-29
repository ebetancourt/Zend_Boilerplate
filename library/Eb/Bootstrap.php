<?php

class Eb_Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    public function __construct($application)
    {
        // Define application root
        defined('APPLICATION_ROOT')
            || define('APPLICATION_ROOT', dirname(dirname(__FILE__)) );

        // Define application root
        defined('LIBRARY_PATH')
            || define('LIBRARY_PATH', APPLICATION_ROOT . DIRECTORY_SEPARATOR . 'library' );

        // Define application root
        defined('EB_CONFIG_PATH')
            || define('EB_CONFIG_PATH', APPLICATION_ROOT . DIRECTORY_SEPARATOR . 'configs' );


        require_once 'Zend/Loader/Autoloader.php';
        $autoloader = Zend_Loader_Autoloader::getInstance();
        $autoloader->setFallbackAutoloader(true);

        parent::__construct($application);
    }
    
    /**
     * Initializes action helpers.
     *
     * @return void
     */
    protected function _initHelpers()
    {

        // add Acl Helper
        $acl       = Eb_Acl::getInstance();
        $aclHelper = new Eb_Controller_Action_Helper_Acl(null, array('acl' => $acl));
        Zend_Controller_Action_HelperBroker::addHelper($aclHelper);
        // Zend_Controller_Action_HelperBroker::addHelper(new Eb_Controller_Action_Helper_Login());
    } // function _initHelpers()

    ////////////////////////////////////////////////////////////////////////////

    /**
     * Initializes action helpers.
     *
     * @return void
     */
    protected function _initDbProfilers()
    {
        $profilerEnvironments = array('local', 'development');

        if (!in_array(APPLICATION_ENV, $profilerEnvironments))
        {
            return;
        }

        $db = Eb_Db_Connection::getConnection();
        $profiler = new Zend_Db_Profiler_Firebug('All DB Queries');
        $profiler->setEnabled(true);
        $db->setProfiler($profiler);
    } // function _initDbProfilers()

    ////////////////////////////////////////////////////////////////////////////

    /**
     * Initializes the ZFDebug plugin.
     *
     * @return void
     */
    protected function _initZFDebug()
    {
        $zfDebugEnvironments = array('local', 'development');

        if (!in_array(APPLICATION_ENV,$zfDebugEnvironments))
        {
            return;
        }

        if (!file_exists(APPLICATION_ROOT . '/modules/default/configs/zfdebug.off'))
        {
            $options = array('jquery_path' => '/js/common/jquery-1.4.2.min.js',
                             'plugins' => array('Variables',
                                                'File' => array('base_path' => APPLICATION_ROOT),
                                                'Memory',
                                                'Time',
                                                'Registry',
                                                'Exception'));
            $debug   = new ZFDebug_Controller_Plugin_Debug($options);

            $this->bootstrap('frontController');
            $frontController = $this->getResource('frontController');
            $frontController->registerPlugin($debug);
        }
    } // function _initZFDebug()
    
}

