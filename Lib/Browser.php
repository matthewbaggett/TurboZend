<?php
require_once(dirname(__FILE__) . "/PhpQuery/phpQuery.php") ;
class Turbo_Lib_Browser{
	
	/**
	 * @param string $url
	 * @return phpQueryObject
	 */
	function get($url){
		return phpQuery::browserGet('http://www.google.com/', function($browser){ 
			return $browser; 
		});
	}
	
	
}