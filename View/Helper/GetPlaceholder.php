<?php
class Turbo_View_Helper_GetPlaceholder extends Zend_View_Helper_Abstract 
{
    public function getPlaceholder ($handle)
    {
        $tbl_placeholders = new CMS_Model_DbTable_Placeholders();
        $obj_placeholder = $tbl_placeholders->fetchRow("strHandle = '{$handle}'");
        return $obj_placeholder->strValue;
    }
}