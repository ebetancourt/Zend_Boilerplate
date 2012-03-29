<?php
/**
 * Eb.
 *
 * @category   Eb
 * @package    Eb_Service
 * @author     Elliot Betancourt <elliot@elliotbetancourt.com>
 */

/**
 * Service that handles user-related tasks.
 *
 * @category   Eb
 * @package    Eb_Service
 * @author     Elliot Betancourt <elliot@elliotbetancourt.com>
 */
class Eb_Service_User
{
    public function __construct()
    {
        $config               = self::_getConfig()->redirect;
        $this->redirectParams = array('module'     => $config->module,
                                      'controller' => $config->controller,
                                      'action'     => $config->action);
    }

    /**
     * Returns the static password hash salt for the site.
     *
     * @return string The salt.
     */
    public static function getSalt()
    {
        return self::_getConfig()->salt;
    } // static function getSalt()

    /**
     * Returns the request object from the front controller
     *
     * @return Zend_Controller_Request_Http The request
     */
    public static function getRequest()
    {
        $front = Zend_Controller_Front::getInstance();
        return $front->getRequest();
    } // static function getRequest()

    /**
     * Returns array route for the login action.
     *
     * @return array The module, controller and action to redirect for login.
     */
    public function getRedirectParams()
    {
        return $this->redirectParams;
    } // function getRedirectParams()

    /**
     * Logs user out of authenticated portion of salt.
     *
     * @return void
     */
    public function logout()
     {
        $zendAuth = self::_getZendAuth();
        $zendAuth->clearIdentity();
        self::redirectUserToLogin();
     } // function logout()

    /**
     * Returns the sites password algorithm for the db.
     *
     * @return string The credential treatment.
     */
    public static function getCredentialTreatment($dynamicSalt = false)
    {
        $inlineSalt = ($dynamicSalt === false)? self::_getConfig()->salt_field : "'$dynamicSalt'";
        $staticSalt = self::getSalt();
        $treatment  = self::_getConfig()->treatment;
        $tmpltVars  = array('{static-salt}', '{dynamic-salt}');
        $tmpltVals  = array($staticSalt, $inlineSalt);

        return str_replace($tmpltVars, $tmpltVals, $treatment);
    } // static function getCredentialTreatment()

    public static function validateUser($email, $password)
    {
        $authAdapter = self::_getAuthAdapter();

        $authAdapter->setIdentity($email)
                    ->setCredential($password);

        $loginAttempt = $authAdapter->authenticate($authAdapter);
        $result = $loginAttempt->isValid();
        
        if ($result)
        {
            $userInfo = new Eb_User();
            self::_getZendAuth()->getStorage()->write($userInfo);
        }
        
        return $result;
    } // static function validateUser($email, $password)

    public function direct()
    {
        $this->requireLogin();
    }

    public static function isLoggedIn()
    {
        $zendAuth = self::_getZendAuth();
        return $zendAuth->hasIdentity();
    } // static function isLoggedIn()

    public static function getAuth()
    {
        return self::_getZendAuth();
    } // static function getAuth()

    public function redirectUserToLogin()
    {
        $config  = self::_getConfig()->redirect;
        $request = self::getRequest();
        $page    = $request->getRequestUri();
        $request->setParam('returnPage', $page);
        $request->setModuleName($config->module)
                ->setControllerName($config->controller)
                ->setActionName($config->action)
                ->setDispatched(false);
    } // static function redirectUser()

    public static function requireLogin()
    {
        if (self::isLoggedIn()){
            // do nothing
            return;
        } else {
            // redirect to login page
            self::redirectUserToLogin();
        }
    } // static function redirectUser()

    private static function _getConfig()
    {
        return Eb_Application_Config::getEb()->auth;
    } // private static function _getConfig()

    private static function _getZendAuth()
    {
        $zendAuth = Zend_Auth::getInstance();
        $zendAuth->setStorage(new Zend_Auth_Storage_Session(self::_getConfig()->namespace));
        return $zendAuth;
    } // private static function _getZendAuth()

    private static function _getAuthAdapter()
    {
        $config  = self::_getConfig();
        $db      = Eb_Db_Connection::getConnection();
        $adapter = new Zend_Auth_Adapter_DbTable($db);
        $adapter->setTableName($config->table);
        $adapter->setIdentityColumn($config->identity);
        $adapter->setCredentialColumn($config->credential);
        $adapter->setCredentialTreatment(self::getCredentialTreatment());

        return $adapter;
    } // private static function _getAuthAdapter()

} // class Eb_Service_User
