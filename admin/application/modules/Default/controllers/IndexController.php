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
 * @package Default
 * @link http://joomlazend.rbsolutions.us
 * @version $Id:$
 */
defined('_JEXEC') or 
    die('Direct Access to this location is not allowed');
/**
 * IndexController
 *
 * Default controller
 *
 *
 * @author rbsolutions (rbsoultions.us@gmail.com)
 * @copyright (c) 2010 Red Black Tree LLC
 * @category ComZend
 * @package Modules
 */
class IndexController extends Zend_Controller_Action{
    /**
     * @var string the current module if one exists
     **/
    protected $_module="";
    /**
     * @var string the name of the controller
     **/
    protected $_name="";
    /**
     * @var string the current action
     **/
    protected $_action="";
    /**
     * @var string the title for the current controller
     */
    protected $_title = "Zend Framework";
    /**
     * @return JApplication stores the current joomla application
     */
    protected $_application=NULL;
    /**
     * init
     *
     * initializes the controller
     **/
    public function init()
    {
        // get the joomla application
        $this->_application =& JFactory::getApplication();
        // get the current module
        $this->_module = $this->getRequest()->getModuleName();
        // get the current controller name
        $this->_name= $this->getRequest()->getControllerName();
        // get the current action
        $this->_action = $this->getRequest()->getActionName();
        return parent::init();
    }
    /**
     * createMenu
     *
     * builds the menu options for this controller
     */
    public function createMenu()
    {
        try {
            if($this->_application->isAdmin()) {
                JToolBarHelper::title($this->_title);
            }
        } catch (Exception $ex) {
            echo "Error creating menu:" . $ex->__toString();
        }
    }
    /**
     * indexAction
     *
     * This is the default controller for the application.
     **/
    public function indexAction()
    {
        try {
            $this->createMenu();
        } catch(exception $ex) {
            echo $ex->__toString();
        }
    }
}

