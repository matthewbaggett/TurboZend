<?php 
class Turbo_Controller_LoggedInAction extends Turbo_Controller_Action{
	public function __construct(Zend_Controller_Request_Abstract $request,
                                Zend_Controller_Response_Abstract $response,
                                array $invokeArgs = array()){
		if(! Turbo_Model_User::getCurrentUser()){
			header("Location: /Login");
			exit;
		}
	}
}