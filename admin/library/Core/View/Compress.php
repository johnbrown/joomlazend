<?php
/**
 * JoomlaZend
 * Zend Framework for Joomla
 * Red Black Tree LLC
 *
 *
 * @author rbsolutions (rbsoultions.us@gmail.com)
 * @copyright (c) 2010 Red Black Tree LLC
 * @category ComZend
 * @package Core
 * @link http://joomlazend.rbsolutions.us
 * @version $Id:$
 */
defined ('_VALID_MOS') or
    die('Direct Access to this location is not allowed');
/**
 * Description of compress
 *
 * tool to help compress output to save bandwidth
 *
 * @author rbsolutions (rbsoultions.us@gmail.com)
 * @copyright (c) 2010 Red Black Tree LLC
 * @category ComZend
 * @package Core
 * @subpackage View
 */
class Core_View_Compress {
    /**
     * @var string the preg expression to remove comments from css files
     */
    protected $_css_preg='!/\*[^*]*\*+([^/][^*]*\*+)*/!';
    /**
     * @var string the preg expression to remove comments from js files
     */
    protected $_js_preg='!/\*[^*]*\*+([^/][^*]*\*+)*/!';
    /**
     * @var array the characters to remove from the file after the comments have been pulled
     */
    protected $_str_replace=array("\r\n","\r","\n","\t","  ","   ");
    /**
     * @var boolean if the system is in debug Mode
     */
    protected $_debug = false;
    /**
     * __construct()
     * 
     * default constructor for the class
     */
    public function  __construct()
    {
        if(Zend_Registry::isRegistered('debug')) {
            $this->_debug = Zend_Registry::get("debug");
        }
    }
    /**
     * compressCSS
     *
     * compresses css that gets included into the file
     */
    public static function compressCSS($buffer)
    {
        $compress = new self();
        if (!$compress->_debug) {
            // Remove comments
            $buffer = preg_replace($compress->_css_preg,'',$buffer);
            // remove tabls, spaces, newline, etc
            $buffer = str_replace($compress->_str_replace,"",$buffer);
        }
        return $buffer;
    }
    /**
     * compressJS
     *
     * compresses js that gets included into the file
     */
    public static function compressJS($buffer)
    {
        $compress = new self();
        if (!$compress->_debug) {
            // Remove comments
            $buffer = preg_replace($compress->_js_preg,'',$buffer);
            // remove tabls, spaces, newline, etc
            $buffer = str_replace($compress->_str_replace,"",$buffer);
         }
        return $buffer;
    }
    /**
     * jquery_encode
     *
     * encodes an object to jquery style
     *
     * @param mixed $data
     * @param boolean $encase
     * @return string jQuery "json" style string
     */
    public static function jquery_encode($data, $encase=true)
    {
        if( is_array($data) || is_object($data) ) {       
        $is_assoc =
          (is_array($data) &&
          0 !== count(array_diff_key($data, array_keys(array_keys($data)))));

        if( $is_assoc || is_object($data) ) {
          $items = Array();
          foreach( $data as $key => $value ) {
            $items[] = Core_View_Compress::jquery_encode($key,false) . ':' . Core_View_Compress::jquery_encode($value,false);
          }
          $json = '{' . implode(',', $items) . '}';
        } else {
          $json = '[' . implode(',', array_map('Core_View_Compress::jquery_encode', $data) ) . ']';
        }
      } else if(is_bool ($data)) {
        if($data===true) {
            $json = "true";
        } else if($data===false) {
            $json = "false";
        }
      } elseif( is_int($data) || is_float($data) ) {
        $json = strtolower($data); # like 10e99
        
      } elseif( null === $data ) {
        $json = 'null';
      } else {
        if($encase) {
            $json = '"' . addcslashes($data, "\"\\\n\r") . '"'; # String
        } else {
            $json = $data; # String
        }
      }
      return $json;
    }
}
