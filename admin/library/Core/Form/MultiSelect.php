<?php
/**
 * ZFJoomla
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
defined('_JEXEC') or 
    die('Direct Access to this location is not allowed');
/**
 * Core_Form_MultiSelect
 *
 * Generates a nice zend form element to replace the multi select
 *
 * @author rbsolutions (rbsoultions.us@gmail.com)
 * @copyright (c) 2010 Red Black Tree LLC
 * @category ComZend
 * @package Core
 * @subpackage Form
 */
class Core_Form_MultiSelect extends Zend_Form_Element_Multiselect {
    /**
     * Use formTextarea view helper by default
     * @var string
     */
    public $helper = 'uiMultiSelect';
}

