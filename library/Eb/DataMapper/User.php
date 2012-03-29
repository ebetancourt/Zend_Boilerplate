<?php
class Eb_DataMapper_User extends Eb_DataMapper_Base
{
    public function getUserById($userId)
    {
		$query = "SELECT * FROM ll_users WHERE id = '$userId'";

		return $this->_db->getRow($query);
    }

    protected $_defaultCacheTime = 300; // 5 minutes
    protected $_cacheTimes       = array();
}
