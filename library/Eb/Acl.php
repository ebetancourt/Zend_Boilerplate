<?php
// based heavily on http://terrarum.net/development/my-zend_acl-implementation.html
class Eb_Acl extends Zend_Acl {

    protected static $_instance;

    private $_noAuth;
    private $_noAcl;

    protected function __construct()
    {
        $config    = self::_getConfig();
        $roles     = $config->acl->roles;
        $resources = $config->acl->resources;

        $this->_addRoles($roles);
        $this->_addResources($resources);
    }

    public function _addRoles($roles)
    {
        foreach ($roles as $name => $parents)
        {
            if (!$this->hasRole($name))
            {
                if (empty($parents))
                {
                    $parents = null;
                }
                else
                {
                    $parents = explode(',', $parents);
                }

                $this->addRole(new Zend_Acl_Role($name), $parents);
             }
         }
    }

    public function _addResources($resources)
    {
        foreach ($resources as $permissions => $controllers)
        {
            foreach ($controllers as $controller => $actions)
            {
                if ($controller == 'all')
                {
                    $controller = null;
                }
                else
                {
                    if (!$this->has($controller))
                    {
                        $this->add(new Zend_Acl_Resource($controller));
                    }
                }

                foreach ($actions as $action => $role)
                {
                    if ($action == 'all')
                    {
                        $action = null;
                    }

                    if ($permissions == 'allow')
                    {
                        $this->allow($role, $controller, $action);
                    }

                    if ($permissions == 'deny')
                    {
                        $this->deny($role, $controller, $action);
                    }
                }
            }
        }
    }

    private static function _getConfig()
    {
        return Eb_Application_Config::getEb();
    } // private static function _getConfig()

    public static function getInstance()
    {
        if (self::$_instance === null) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }
}
