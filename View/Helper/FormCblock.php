<?php

/**
 * Abstract class for extension
 */
require_once 'Zend/View/Helper/FormElement.php';


class Zend_View_Helper_FormCblock extends Zend_View_Helper_FormElement
{
    /**
     * The default number of rows for a textarea.
     *
     * @access public
     *
     * @var int
     */
    public $rows = 24;

    /**
     * The default number of columns for a textarea.
     *
     * @access public
     *
     * @var int
     */
    public $cols = 80;

    /**
     * Generates a 'textarea' element.
     *
     * @access public
     *
     * @param string|array $name If a string, the element name.  If an
     * array, all other parameters are ignored, and the array elements
     * are extracted in place of added parameters.
     *
     * @param mixed $value The element value.
     *
     * @param array $attribs Attributes for the element tag.
     *
     * @return string The element XHTML.
     */
    public function formCblock($name, $value = null, $attribs = null)
    {
        $info = $this->_getInfo($name, $value, $attribs);
        extract($info); // name, value, attribs, options, listsep, disable

        // is it disabled?
        $disabled = '';
        if ($disable) {
            // disabled.
            $disabled = ' disabled="disabled"';
        }

        // Make sure that there are 'rows' and 'cols' values
        // as required by the spec.  noted by Orjan Persson.
        if (empty($attribs['rows'])) {
            $attribs['rows'] = (int) $this->rows;
        }
        if (empty($attribs['cols'])) {
            $attribs['cols'] = (int) $this->cols;
        }

        $str_xsl_path = getcwd()."/../library/Turbo/CMS/xsl/editor.xsl";
    	if(!file_exists($str_xsl_path)){
    		throw new Exception("Cannot find XSL file: {$str_xsl_path}");
    	}
    	$str_xsl = file_get_contents($str_xsl_path);
    	
    	// Process the XML through an XSLT stylesheet
    	$xslt = new XSLTProcessor();
   		$xslt->importStylesheet(new  SimpleXMLElement($str_xsl));
   		$str_transformed = $xslt->transformToXml(new SimpleXMLElement($value));
           		
        // build the element
        $xhtml = '<div class="cblock-editor">'
        		. '<div class="pane editor">'
        		. $str_transformed
        		. '</div>'
        		. '<div class="pane raw">'
        		. '<textarea name="' . $this->view->escape($name) . '"'
                . ' id="' . $this->view->escape($id) . '"'
                . $disabled
                . $this->_htmlAttribs($attribs) . '>cblock'
                . $this->view->escape($value) . '</textarea>'
                . '</div>'
                . '</div>';

        return $xhtml;
    }
}
