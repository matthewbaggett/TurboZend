<?php
require_once(dirname(__FILE__) . "/SimpleTest/browser.php") ;
require_once(dirname(__FILE__) . "/SimpleHtmlDom/simple_html_dom.php")
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
	
	static function getParsableContent(SimpleBrowser $browser){
		$shd = new simple_html_dom();
		$rawhtml = $browser->getContent();
		
		$page = $shd->load($rawhtml);
		return $page;
	}
	
	
}