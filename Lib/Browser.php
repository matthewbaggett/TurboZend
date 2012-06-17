<?php
require_once(dirname(__FILE__) . "/SimpleTest/browser.php") ;
require_once(dirname(__FILE__) . "/SimpleHtmlDom/simple_html_dom.php");

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
	
	/**
	 * Feed a SimpleBrowser to this static class, get a simple_html_dom back
	 * @param SimpleBrowser $browser
	 * @return simple_html_dom
	 */
	static function getParsableContent(SimpleBrowser $browser){
		$page = new simple_html_dom();
		$rawhtml = $browser->getContent();
		
		$page->load($rawhtml);
		return $page;
	}
	
	
}