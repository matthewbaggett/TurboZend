<?php 
class Application_Model_Favourite extends Zend_Db_Table_Row_Abstract{
	
	public function getUser(){
		return Application_Model_User::getUser($this->intUserID);
	}
}
