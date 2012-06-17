<?php 
class Turbo_Model_Setting extends Zend_Db_Table_Row_Abstract{
	static public function get($setting_name){
		$tblSettings = new Turbo_Model_DbTable_Settings();
		$sel = $tblSettings->select(true);
		$sel->where('strName = ?', $setting_name);
		$o_setting = $tblSettings->fetchRow($sel);
		if($o_setting){
			return $o_setting;
		}else{
			self::set_setting($setting_name,null);
			return self::get($setting_name);
		}
	}
	
	static public function set_setting($setting_name,$setting_value = null){
		
	}
	
}
