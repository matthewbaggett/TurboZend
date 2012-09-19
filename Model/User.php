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
	
	public function settingGet($key, $max_age_sec = -1){
		$oSetting = $this->settingFetch($key);
		if(!is_object($oSetting)){
			return FALSE;
		}
		$max_age_timestamp = time() - $max_age_sec;
		if(strtotime($oSetting->dtmSaved) >= $max_age_timestamp || $max_age_sec < 0){
			$decoded = base64_decode($oSetting->strValue);
			$unserialized = unserialize($decoded);
			if($unserialized == false){
				//throw new exception("Could not unserialize this setting: {$oSetting->intUserSettingID} {$oSetting->strKey}: {$oSetting->strValue}");
			}
			return $unserialized;
		}else{
			// TOO OLD >:|
			return FALSE;
		}
	}
	
	public function settingSet($key, $value){
		$value = base64_encode(serialize($value));
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
			$oSetting->dtmSaved = date("Y-m-d H:i:s");
			$oSetting->save();
			return TRUE;
		}catch(Exception $e){
			return FALSE;
		}
	}
}
