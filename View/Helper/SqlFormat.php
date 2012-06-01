<?php
class Turbo_View_Helper_SqlFormat extends Zend_View_Helper_Abstract 
{
    public function sqlFormat ($sql)
    {
    	if(!defined("PARSER_LIB_ROOT")){
    		define("PARSER_LIB_ROOT", dirname(__FILE__)."/../../Lib/SQLParser/");
    		require_once(PARSER_LIB_ROOT . "sqlparser.lib.php");
    	}
        
        return PMA_SQP_formatHtml(PMA_SQP_parse($sql));
    }
}