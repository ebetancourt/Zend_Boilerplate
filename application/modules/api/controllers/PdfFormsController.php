<?php

class Api_PdfFormsController extends Zend_Controller_Action
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


    public function creditApplicationAction()
    {
        $content = '';

        // instead of the code below here, you should insert the values into a db
        foreach ($this->getRequest()->getParams() as $key => $value)
        {
           $content     .= "$key: $value\n";
           $fields[$key] = $value;
        }

        $this->view->fields = $fields;
        error_log($content, 0);
    }

    public function preDispatch()
    {
        //$this->getResponse() ->setHeader('Content-Type', 'application/vnd.fdf');
    }
}

