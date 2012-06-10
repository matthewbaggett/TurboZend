<?php
require_once(dirname(__FILE__) . "/SimpleTest/browser.php") ;
class Turbo_Lib_Browser{
	
	/**
	 * @param string $url
	 * @return phpQueryObject
	 */
	static function get($url){
		$browser = &new SimpleBrowser();
		$browser->get($url);
		return $browser;
	}
	
	
}