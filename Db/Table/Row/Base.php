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
}