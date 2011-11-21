<?php
require_once(dirname(__FILE__) . "/../../../application/controllers/LayoutController.php");
class Turbo_Controller_Action extends LayoutController
{

	private function _send_error_email($errors){
		$mail = new Zend_Mail();
        $mail->addTo(Zend_Registry::get('config')->site->exceptions_email);
        $mail->setSubject("Exception! - ".date("Y-m-d H:i:s")." - {$this->view->message}");
        $mail->setBodyText($_SERVER['HTTP_HOST']. $_SERVER['REQUEST_URI'] . "\n\n" . $errors->exception."\n\n\$_SERVER:\n".print_r($_SERVER,true)."\n\n\$_REQUEST:\n".print_r($_REQUEST,true));
        $mail->send();
	}
    public function errorAction()
    {
        $errors = $this->_getParam('error_handler');
        
        if (!$errors) {
            $this->view->message = 'You have reached the error page';
            return;
        }
        
        switch ($errors->type) {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
        
                // 404 error -- controller or action not found
                $this->getResponse()->setHttpResponseCode(404);
                $this->view->message = 'Page not found';
                break;
            default:
                // application error
                $this->getResponse()->setHttpResponseCode(500);
                $this->view->message = 'Application error';
                break;
        }
        
        // Log exception, if logger available
        if ($log = $this->getLog()) {
            $log->crit($this->view->message, $errors->exception);
        }
        
        switch(true){
        	// Fucking Google & Baidu, exploding my email inbox..
        	case (stripos($_SERVER['HTTP_USER_AGENT'],'Googlebot') !== FALSE):
        	case (stripos($_SERVER['HTTP_USER_AGENT'],'Baiduspider') !== FALSE):
        		break;
        	default:
        		$this->_send_error_email($errors);
        }
        
        
        // conditionally display exceptions
        if ($this->getInvokeArg('displayExceptions') == true) {
            $this->view->exception = $errors->exception;
        }
        
        $this->view->request   = $errors->request;
    }

    public function getLog()
    {
        $bootstrap = $this->getInvokeArg('bootstrap');
        if (!$bootstrap->hasResource('Log')) {
            return false;
        }
        $log = $bootstrap->getResource('Log');
        return $log;
    }


}

