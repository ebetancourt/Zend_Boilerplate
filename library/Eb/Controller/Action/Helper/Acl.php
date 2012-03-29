<?php

// based heavily on http://terrarum.net/development/my-zend_acl-implementation.html
class Eb_Controller_Action_Helper_Acl extends Zend_Controller_Action_Helper_Abstract {

    protected $_action;
    protected $_auth;
    protected $_acl;
    protected $_controllerName;

    public function __construct(Zend_View_Interface $view = null, array $options = array()) {
        $this->_auth = Eb_Service_User::getAuth();
        $this->_acl  = $options['acl'];
    }

    public function init()
    {
        $this->_action = $this->getActionController();
        $controller    = $this->_action->getRequest()->getControllerName();
    }

    public function preDispatch()
    {
        $role = 'guest';

        if ($this->_auth->hasIdentity())
        {
            $user = $this->_auth->getIdentity();

            if (is_object($user))
            {
                $role = $this->_auth->getIdentity()->role;
            }
        }
        Eb_Log::getInstance()->log('The user role is: ' . $role);
        $config     = Eb_Application_Config::getEb();
        $request    = $this->_action->getRequest();
        $controller = $request->getControllerName();
        $action     = $request->getActionName();
        $module     = $request->getModuleName();
        $resource   = $module . '_' . $controller;
        $privilege  = $action;

        $this->_controllerName = $controller;

        // Check first for this user type's permissions for <module>_<controller>
        if (!$this->_acl->has($resource))
        {
            $resource = $module;
        }

        // Next, check for this user type's permissions for <module>
        if (!$this->_acl->has($resource))
        {
            $resource = $controller;
        }

        // Next, check for this user type's permissions for <controller>
        if (!$this->_acl->has($resource))
        {
            $resource = null;
        }

        if (!$this->_acl->isAllowed($role, $resource, $privilege))
        {
            $request->setModuleName($config->auth->redirect->module)
                    ->setControllerName($config->auth->redirect->controller)
                    ->setActionName($config->auth->redirect->action)
                    ->setDispatched(false);
        }
    }
} // class Eb_Controller_Action_Helper_Acl
