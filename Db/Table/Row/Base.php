<?php 

class Turbo_Db_Table_Row_Base extends Zend_Db_Table_Row_Abstract{
	public function delete(){
		if(isset($this->bolDeleted)){
			$this->bolDeleted = TRUE;
			try{
				$this->save();
			}catch(Exception $e){
				// Nuffing.
			}
			return TRUE;
		}else{
			parent::delete();
			return TRUE;
		}
	}
	
	public function __turbo_has_virtual_method($method){
		if($this->__call_get_method($method) !== FALSE){
			return TRUE;
		}else{
			return FALSE;
		}
	}
	
	protected function __call_get_method($method){
		if (preg_match('/^([get|set]+)_(.*)/', $method, $matches)) {
            $index  = null;
            $action = $matches[1];
            $variable = $matches[2];
            if(isset($this->variable_map[$variable])){
            	$arr_action = array('action' => $action, 'variable' => $variable);
            	return $arr_action;	
            }else{
            	return FALSE;
            }
		}
		return FALSE;
	}
	
	protected function __call_do_action($action, $variable, $arguments){
		
		switch($action){
			case 'get':
				$str_column = $this->variable_map[$variable];
				return $this->$str_column;
				break;
			case 'set':
				$str_column = $this->variable_map[$variable];
				$this->$str_column = $arguments[0];
				break;
		}
	}
	
	public function __call($method, $arguments){
		
		if($arr_action = $this->__call_get_method($method)){
			return $this->__call_do_action($arr_action['action'], $arr_action['variable'], $arguments);
		}
		return parent::__call($method, $arguments);
	}
}