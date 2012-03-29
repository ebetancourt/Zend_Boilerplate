<?php

class Cms_LoginController extends Zend_Controller_Action
{
    public function indexAction()
    {
        // If we're already logged in, just redirect
        if(Zend_Auth::getInstance()->hasIdentity())
        {
            $this->_redirect('/cms');
        }

        $request      = $this->getRequest();
        $loginForm    = $this->getLoginForm();
        $errorMessage = '';

        if($request->isPost())
        {
            if($loginForm->isValid($request->getPost()))
            {
                // get the username and password from the form
                $username = $loginForm->getValue('username');
                $password = $loginForm->getValue('password');

                if(Eb_Service_User::validateUser($username, $password))
                {
                    $errorMessage = "Successful login!";
                } else  {
                    $errorMessage = "Wrong username or password provided. Please try again.";
                }
            }
        }

        $this->view->errorMessage = $errorMessage;
        $this->view->loginForm = $loginForm;
    }

    public function logoutAction()
    {
        Eb_Service_User::logout();
    }

    protected function getLoginForm()
    {
        $username = new Zend_Form_Element_Text('username');
        $username->setLabel('Username:')
                ->setRequired(true);

        $password = new Zend_Form_Element_Password('password');
        $password->setLabel('Password:')
                ->setRequired(true);

        $submit = new Zend_Form_Element_Submit('login');
        $submit->setLabel('Login');

        $loginForm = new Zend_Form();
        $loginForm->setAction('/cms/login/index/')
                ->setMethod('post')
                ->addElement($username)
                ->addElement($password)
                ->addElement($submit);

        return $loginForm;
    }

}