<?php

class Turbo_Db_Table_Abstract extends Zend_Db_Table_Abstract
{
	static public function factory(){
		$called_name = get_called_class();
		return new $called_name();
	}
	public function select($withFromPart = self::SELECT_WITHOUT_FROM_PART){
		$select = parent::select($withFromPart);
		$select->where('bolDeleted = 0');
		return $select;
	}
	
}

