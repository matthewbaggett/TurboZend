<?php 
class Turbo_Model_User extends Turbo_Db_Table_Row_Base{
	
	protected $arr_seen_image_ids = null;
	protected $arr_favourite_tag_ids = null;
	protected $arr_favourite_ids = null;
	
	const ANONYMOUS_USER_ID = -2;
	
	/**
	 * Throws out an object of the current user
	 * 
	 * @return Turbo_Model_User
	 */
	static public function getCurrentUser($return_anons = false){
		if(Zend_Auth::getInstance()->hasIdentity()){
			$int_user_id = Zend_Auth::getInstance()->getIdentity()->intUserID;
			return self::getUser($int_user_id);
		}elseif($return_anons){
			return self::getUser(self::ANONYMOUS_USER_ID);
		}else{
			return FALSE;
		}
	}
	
	static public function getUser($int_user_id){
		$tbl_users = new Turbo_Model_DbTable_Users();
		return $tbl_users->fetchRow("intUserID = {$int_user_id}");
	}
	
	public function isLoggedInUserAdmin(){
		switch($this->enumLevel){
			case 'superadmin':
			case 'admin':
				return TRUE;
			default:
				return FALSE;
		}
	}
	
	static public function enforceLogin(){
		if(!Zend_Auth::getInstance()->hasIdentity()){
			header("Location: /Login");
			exit;
		}
	}
	
	public function settingFetch($key){
		$tblUserSettings = new Turbo_Model_DbTable_UserSettings();
		$sel_setting = $tblUserSettings->select(true);
		$sel_setting->where("intUserID = ?", $this->intUserID);
		$sel_setting->where("strKey = ?", $key);
		return $tblUserSettings->fetchRow($sel_setting);
	}
	
	public function settingGet($key){
		$oSetting = $this->settingFetch($key);
		return unserialize($oSetting->strValue);
	}
	
	public function settingSet($key, $value){
		$value = serialize($value);
		try{
			$oSetting = $this->settingFetch($key);
			if(!$oSetting){
				$tblUserSettings = new Turbo_Model_DbTable_UserSettings();
				$int_insert_id = $tblUserSettings->insert(array(
						'intUserID' => $this->intUserID,
						'strKey' => $key,
						'strValue' => $value,
						));
				$oSetting = $this->settingFetch($key);
			}
			$oSetting->strValue = $value;
			return TRUE;
		}catch(Exception $e){
			return FALSE;
		}
	}
}
