<?php 
class Turbo_Controller_LoggedInAction extends Turbo_Controller_Action{
	public function __construct(){
		if(! Turbo_Model_User::getCurrentUser()){
			header("Location: /Login");
			exit;
		}
	}
}