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
 * Core_View_Helper_Jurl
 *
 * creates a JRoute compatible url 
 *
 * @author rbsolutions (rbsoultions.us@gmail.com)
 * @copyright (c) 2010 Red Black Tree LLC
 * @category ComZend
 * @package Core
 * @subpackage View_Helper
 */
class Core_View_Helper_Jurl extends Zend_View_Helper_Abstract {
    /**
     * Jurl
     * 
     * creates a Joomla friendly URL
     * 
     * @param array $args
     * @param type $route
     * @param type $clear 
     */
    public function Jurl(array $args = array(),$route='default',$clear=false)
    {
        $ZendResult = urldecode(substr($this->view->url($args,$route,$clear),1));
        $mdlMenu = new Model_Menu();
        $menuItem = $mdlMenu->getByLink($ZendResult);
        if($menuItem==NULL) {
            return JRoute::_("/".$ZendResult,true);
        } else {
            return JRoute::_("index.php?Itemid=".$menuItem->id,true);
        }
    }
}

