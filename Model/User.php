<?php 
class Turbo_Model_User extends Turbo_Db_Table_Row_Base{
	
	protected $arr_seen_image_ids = null;
	protected $arr_favourite_tag_ids = null;
	protected $arr_favourite_ids = null;
	
	const ANONYMOUS_USER_ID = -2;
	/**
	 * Throws out an object of the current user
	 * 
	 * @return Application_Model_User
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
		$tbl_users = new Application_Model_DbTable_Users();
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
	
	/*
	 * Logic to get Favourite IMAGES
	 */
	public function getFavourites(){
		$tbl_favourites = new Application_Model_DbTable_Favourites();
		return $tbl_favourites->fetchAll("intUserID = {$this->intUserID}", 'intFavouriteID DESC');
	}
	public function getFavouritesArray(){
		if(is_null($this->arr_favourite_ids)){
			$arr_favourites = $this->getFavourites();
			$this->arr_favourite_ids = array();
			foreach($arr_favourites as $arr_favourite){
				$this->arr_favourite_ids[] = $arr_favourite['intImageID'];
			}
		}
		return $this->arr_favourite_ids;
	}

	/*
	 * Logic to get Favourite TAGS
	 */
	public function getFavouriteTags(){
		$tbl_favourite_tags = new Application_Model_DbTable_FavouriteTags();
		return $tbl_favourite_tags->fetchAll("intUserID = {$this->intUserID}", 'intTagID DESC');
	}
	public function getFavouriteTagsArray(){
		if(is_null($this->arr_favourite_tag_ids)){
			$arr_favourite_tags = $this->getFavouriteTags();
			$this->arr_favourite_tag_ids = array();
			foreach($arr_favourite_tags as $arr_favourite_tag){
				$this->arr_favourite_tag_ids[] = $arr_favourite_tag['intTagID'];
			}
		}
		return $this->arr_favourite_tag_ids;
	}
	
	/*
	 * Logic to get seen images
	 */
	public function getSeenImages(){
		$tbl_image_views = new Application_Model_DbTable_ImageViews();
		$sel_seen = $tbl_image_views->select(true);
		$sel_seen->where("intUserID = {$this->intUserID}");
		$sel_seen->group('intImageID');
		return $tbl_image_views->fetchAll($sel_seen);
	}
	public function getSeenImagesArray(){
		if(is_null($this->arr_seen_image_ids)){
			$arr_seen_images = $this->getSeenImages();
			$this->arr_seen_image_ids = array();
			foreach($arr_seen_images as $arr_seen_image){
				$this->arr_seen_image_ids[] = $arr_seen_image['intImageID'];
			}
		}
		return $this->arr_seen_image_ids;
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
		return $tblUserSettings->fetchOne($sel_setting);
	}
	public function settingGet($key){
		$oSetting = $this->settingFetch($key);
		return $oSetting->strValue;
	}
	
	public function settingSet($key, $value){
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
