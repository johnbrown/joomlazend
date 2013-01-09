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
 * @package Admin
 * @link http://joomlazend.rbsolutions.us
 * @version $Id:$
 */
defined ('_VALID_MOS') or
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
 * @package Admin
 * @subpackage Controller
 */
class Admin_IndexController extends Core_Controller_Action{
    /**
     * @var string the title for the current controller
     */
    protected $_title = "Zend Framework Admin";
    /**
     * createMenu
     *
     * builds the menu options for this controller
     */
    public function createMenu()
    {
        try {
            if($this->_application->isAdmin()) {
                // top menu options
                JToolBarHelper::title($this->view->translate->_($this->_title));

                // main menu options
                $menuItems = array();
                $count = 0;
                // add menu Items
                // config menu item
                $menuItems[$count++] = array(
                    'link'=>JRoute::_($this->view->url(array(
                            'module'=>'Admin',
                            'controller'=>'config',
                            'action'=>'index',
                        ),'administrator',true)),
                    'icon'=>'../../../components/'.ZEND_COMPONENT_NAME
                        .'/application/public/images/icons/48x48/configure.png',
                    'text'=>'Config',
                );
                // Manage Modules
                $menuItems[$count++] = array(
                    'link'=>JRoute::_($this->view->url(array(
                            'module'=>'Admin',
                            'controller'=>'modules',
                            'action'=>'index',
                        ),'administrator',true)),
                    'icon'=>'../../../components/'.ZEND_COMPONENT_NAME
                        .'/application/public/images/icons/48x48/emblem-package.png',
                    'text'=>'Manage Modules',
                );
                $mdlModules = new Model_ZFModules();
                $modules = $mdlModules->getModules();
                if($modules!=NULL) {
                    foreach($modules as $module) {
                        if(file_exists(APPLICATION_PATH.DIRECTORY_SEPARATOR.'modules'
                                .$module->module.DIRECTORY_SEPARATOR
                                .'images'.DIRECTORY_SEPARATOR.'logo.png')) {
                            $icon='../../../components/'.ZEND_COMPONENT_NAME
                                ."/application/modules".$module->module."/images/logo.png";
                        } else {
                            $icon='../../../components/'.ZEND_COMPONENT_NAME
                                .'/application/public/images/icons/48x48/emblem-package.png';
                        }
                        $menuItems[$count++] = array(
                            'link'=>JRoute::_($this->view->url(array(
                                'module'=>substr($module->module,1),
                                'controller'=>'index',
                                'action'=>'adminindex',
                            ),'administrator')),
                            'icon'=>$icon,
                            'text'=>substr($module->module,1),
                        );
                    }
                }

                // send the menu items to the view
                $this->view->menuItems=$menuItems;
            }
        } catch (Exception $ex) {
            echo "Error creating menu:" . $ex->__toString();
        }
    }
    /**
     * indexAction
     *
     * Default Action for the Controller
     **/
    public function indexAction()
    {
        try {
            $this->createMenu();
            $mdlUser = new Model_User();
        } catch(exception $ex) {
            echo $ex->__toString();
        }
    }
}

