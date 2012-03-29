<?php

class Cms_IndexController extends Zend_Controller_Action
{

    public function init()
    {
    }

    public function preDispatch()
    {
        Eb_Service_User::requireLogin();
    }

    public function indexAction()
    {
        // action body
    }


}

