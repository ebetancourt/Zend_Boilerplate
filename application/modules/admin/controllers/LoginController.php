<?php
class Admin_LoginController extends Zend_Controller_Action
{

    public function init()
    {
        $this->_userService = new Eb_Service_User();
    }

    public function indexAction()
    {
        $request    = $this->getRequest();
        $returnPage = $request->getParam('returnPage', '/');
        $message    = '';
        $email      = '';
        $msgStyle   = 'error';

        if ($request->isPost())
        {
            $loginForm  = $request->getParam('login', array());
            $returnPage = $loginForm['returnPage'];
            $email      = $loginForm['username'];
            $password   = $loginForm['password'];

            if ($this->_userService->validateUser($email, $password))
            {
                $this->_redirect($returnPage);
            } else {
                $message = 'incorrect login credentials';
            }
        }

        $this->view->actionPage = $this->_userService->getRedirectParams();
        $this->view->message    = $message;
        $this->view->email      = $email;
        $this->view->returnPage = $returnPage;
    }

    public function applyAction()
    {
        // action body
    }

    public function logOutAction()
    {
        $this->_userService->logout();
    }

}
