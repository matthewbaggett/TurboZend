<?php 
class Turbo_Model_UserSetting extends Turbo_Db_Table_Row_Base{
	
	public function getType(){
		$tblUserSettingTypes = new Turbo_Model_DbTable_UserSettingTypes();
		$o_setting_type = $tblUserSettingTypes->fetchRow("strKey = '{$this->strKey}'");
		return $o_setting_type->strType;
	}
}
