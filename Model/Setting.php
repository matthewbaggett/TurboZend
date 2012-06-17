<?php
class Turbo_Model_Setting extends Zend_Db_Table_Row_Abstract{
	static public function get($setting_name){
		$tblSettings = new Turbo_Model_DbTable_Settings();
		$sel = $tblSettings->select(true);
		$sel->where('strName = ?', $setting_name);
		$o_setting = $tblSettings->fetchRow($sel);
		if($o_setting){
			return unserialize($o_setting->strValue);
		}else{
			self::set($setting_name,null);
			return unserialize(self::get($setting_name)->strValue);
		}
	}

	static public function set($setting_name,$setting_value = null){
		$tblSettings = new Turbo_Model_DbTable_Settings();
		$sel = $tblSettings->select(true);
		$sel->where('strName = ?', $setting_name);
		$o_setting = $tblSettings->fetchRow($sel);
		if($o_setting){
			$o_setting->strValue = serialize($setting_value);
			$o_setting->save();
		}else{
			$tblSettings->insert(array(
				'strName' => $setting_name,
				'strValue' => serialize($setting_value)
			));
		}
	}

}
