<?php

class Turbo_Controller_Login extends Zend_Controller_Action
{

    protected function _getUserAuthAdapter()
    {
		$dbAdapter = Zend_Db_Table::getDefaultAdapter();
		$authAdapter = new Zend_Auth_Adapter_DbTable($dbAdapter);

		$authAdapter->setTableName('tblUsers')
		->setIdentityColumn('strUsername')
		->setCredentialColumn('strPassword')
		->setCredentialTreatment('SHA1(?)');

		return $authAdapter;
    }

    protected function _getEmailAuthAdapter()
    {
		$dbAdapter = Zend_Db_Table::getDefaultAdapter();
		$authAdapter = new Zend_Auth_Adapter_DbTable($dbAdapter);

		$authAdapter->setTableName('tblUsers')
		->setIdentityColumn('strEmail')
		->setCredentialColumn('strPassword')
		->setCredentialTreatment('SHA1(?)');
		
		return $authAdapter;
    }

    protected function _process_login($values)
    {
		// Get our authentication adapter and check credentials
		
		/**
		 * Try username
		 */
		$adapters['username'] = $this->_getUserAuthAdapter();
		$adapters['email'] = $this->_getEmailAuthAdapter();
		foreach($adapters as $adapter){
			$adapter->setIdentity($values['username']);
			$adapter->setCredential($values['password']);
	
			$auth = Zend_Auth::getInstance();
			$result = $auth->authenticate($adapter);
			if ($result->isValid()) {
				$user = $adapter->getResultRowObject();

				// Do on-login events here...
				
				$auth->getStorage()->write($user);
				$tbl_user_logins = new Turbo_Model_DbTable_UserLogins();
				
				$tbl_user_logins->insert(array(
					'intUserID' => $user->intUserID,
					'dtmTime' => date("Y-m-d H:i:s"),
					'strIPAddress' => $_SERVER['REMOTE_ADDR'],
				));
				return true;
			}
		}
		return false;
    }
    protected function _make_activation_key($length=10, $strength=0){
		$vowels = 'aeuy';
		$consonants = 'bdghjmnpqrstvz';
		if ($strength & 1) {
			$consonants .= 'BDGHJLMNPQRSTVWXZ';
		}
		if ($strength & 2) {
			$vowels .= "AEUY";
		}
		if ($strength & 4) {
			$consonants .= '23456789';
		}
		if ($strength & 8) {
			$consonants .= '@#$%';
		}
	 
		$password = '';
		$alt = time() % 2;
		for ($i = 0; $i < $length; $i++) {
			if ($alt == 1) {
				$password .= $consonants[(rand() % strlen($consonants))];
				$alt = 0;
			} else {
				$password .= $vowels[(rand() % strlen($vowels))];
				$alt = 1;
			}
		}
		return $password;
    }
    protected function _process_registration($values,Application_Form_Register $form){
    	if($values['password'] != $values['password_again']){
    		$form->getElement('password')->addError('Passwords do not match');
    		$form->getElement('password_again')->addError('Passwords do not match');
    		return;
    	}
    	$str_activation_key = $this->_make_activation_key();
    	$tbl_users = new Turbo_Model_DbTable_Users();
    	$salt = $this->_make_activation_key(40,16);
    	$int_insert_id = $tbl_users->insert(array(
    		'strUsername' => $values['username'],
			'strPassword' => hash("SHA1", $values['password'] ),
			'strEmail' => $values['email'],
			//'salutation'
			'strFirstname' => $values['firstname'],
			'strSurname' => $values['surname'],
			'strNickname' => $values['username'],
			//'date_of_birth' => 
			'dtmRegistered' => date("Y-m-d H:i:s"),
			'enumLevel' => 'basic',
			'enumActive' => 'inactive',
    		'strActivationKey' => $str_activation_key,
    	));
    	
    	$obj_user = $tbl_users->fetchRow('intUserID = '.$int_insert_id);
    	
    	$welcome_email = new Zend_Mail();
    	$welcome_email->addTo($obj_user->strEmail);
    	$welcome_email->setSubject("Welcome to Gamitude, {$obj_user->strUsername}!");
    	
    	$activation_url = "http://" . $_SERVER['SERVER_NAME'] . $this->view->url(array('controller' => 'Login', 'action' => 'activation', 'key' => $str_activation_key),null,true);
    	$welcome_email->setBodyText("Hey {$obj_user->strUsername}! Your account is one step away from activation!\n\n You can activate your account by clicking here: {$activation_url}\n\nToodles!\n\n -- Pilot");
    	$welcome_email->send();
    	
    	// Get all the admin email addresses, send them a "new user!" email
    	$arr_admin_users = $tbl_users->fetchAll(array("enumActive = 'active'","enumLevel IN ('admin','superadmin')"));
    	foreach($arr_admin_users as $obj_admin_user){
	    	$new_user_email = new Zend_Mail();
	    	$new_user_email->addTo($obj_admin_user->strEmail);
	    	$new_user_email->setSubject("New user signed up: {$obj_user->strUsername}!");
	    	$new_user_email->setBodyText(implode("\n",$obj_user));
	    	$new_user_email->send();
	    	unset($new_user_email);
    	}
    }
    
    protected function _login_redirect(){
    	$this->_helper->redirector('index', 'index');
    }

    public function init()
    {
		/* Initialize action controller here */
    }

    public function indexAction()
    {
		$form = new Application_Form_Login();
		$request = $this->getRequest();
		if ($request->isPost()) {
			if ($form->isValid($request->getPost())) {
				if ($this->_process_login($form->getValues())) {
					// We're authenticated! Redirect to the home page
					$this->_login_redirect();
				}
			}
		}
		$this->view->form = $form;
    }

    public function logoutAction()
    {
		Zend_Auth::getInstance()->clearIdentity();
		$this->_helper->redirector('index','index'); // back to login page
    }

    public function registerAction()
    {
         $form = new Application_Form_Register();
         $request = $this->getRequest();
         if ($request->isPost()) {
             if ($form->isValid($request->getPost())) {
                 $this->_process_registration($form->getValues(),$form);
                 $this->_helper->redirector('welcome', 'Login');
             }
         }
         $this->view->form = $form;
    }
    
    public function welcomeAction(){}
    public function activatedAction(){}
    
    public function activationAction(){
    	$str_activation_key = $this->_getParam('key');
    	$tbl_users = new Turbo_Model_DbTable_Users();
    	$obj_user = $tbl_users->fetchRow("strActivationKey = '{$str_activation_key}'");
    	//print_r($obj_user);exit;
    	$obj_user->enumActive = 'active';
    	$obj_user->save();
    	
    	// Get all the admin email addresses, send them an "activated user!" email
    	$arr_admin_users = $tbl_users->fetchAll(array("enumActive = 'active'","enumLevel IN ('admin','superadmin')"));
    	foreach($arr_admin_users as $obj_admin_user){
    		$activated_user_email = new Zend_Mail();
    		$activated_user_email->addTo($obj_admin_user->strEmail);
    		$activated_user_email->setSubject("New user activated: {$obj_user->strUsername}!");
    		$activated_user_email->setBodyText(implode("\n",$obj_user));
    		$activated_user_email->send();
    		unset($activated_user_email);
    	}
    	
    	$this->_helper->redirector('activated', 'Login');
    }


}





