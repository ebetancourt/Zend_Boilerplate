<?php

class Cms_EditPostController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // $db = Eb_Db_Connection::getConnection();
        // print_r($db->fetchAll('select * from clients'));
    }


    public function textAction()
    {
    }

    public function preDispatch()
    {
        //$this->getResponse() ->setHeader('Content-Type', 'application/vnd.fdf');
    }
}

