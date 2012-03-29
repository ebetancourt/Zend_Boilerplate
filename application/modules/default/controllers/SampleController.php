<?php

class SampleController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // action body
	$this->view->message = "Hello World";
       $db = Eb_Db_Connection::getConnection();
$sql = 'SELECT * FROM test WHERE id = ?';
 

$this->view->results = $result = $db->fetchAll($sql, 1);
    }
    public function sampleAction()
    {
        // action body
	$this->view->message = "Hello World";
       $db = Eb_Db_Connection::getConnection();
$sql = 'SELECT * FROM test WHERE id = ?';
 

$this->view->results = $result = $db->fetchrow($sql, "2");
    }


}

