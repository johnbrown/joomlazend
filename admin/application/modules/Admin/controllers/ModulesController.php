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
 * ModulesController
 *
 * Controlls the install and management of Zend Framework Based Modules
 *
 *
 * @author rbsolutions (rbsoultions.us@gmail.com)
 * @copyright (c) 2010 Red Black Tree LLC
 * @category ComZend
 * @package Admin
 * @subpackage Controller
 */
class Admin_ModulesController extends Core_Controller_Action
{
    /**
     * @var string the title for the current controller
     */
    protected $_title = "Zend Framework Modules";
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
        if($this->_application->isAdmin()) {
            // create the menu
            try {
                $this->createMenu();
            } catch(exception $ex) {
                echo $ex->__toString();
            }
            // gather the data
            $mdlModules = new Model_ZFModules();
            $this->view->row = $mdlModules->getModules();
            // create the install form
            try {
                $frmInstall = new Admin_Form_InstallModule();
                $this->view->form = $frmInstall;
            } catch (exception $ex) {
                die("Error:".$ex->getMessage());
            }
        } else {
            $this->_redirect($this->view->url());
        }
    }
    /**
     * installAction
     * 
     * installs a new module into the system
     */
    public function installAction()
    {
        // location to send user to on an error
        $errorRedirect = JRoute::_($this->view->url(array(
                    'controller'=>$this->_name,
                    'action'=>'index',
                ),'administrator'));
        // check to see if the form was submitted
        if($this->_getParam('Submit')== NULL
                || $this->_getParam('Submit')!='Install') {
            $this->_redirect($errorRedirect."&warning="
                .urlencode($this->view->translate->_("Invalid File Provided")));
        }
        // create the install form
        try {
            $frmInstall = new Admin_Form_InstallModule();
            $this->view->form = $frmInstall;
        } catch (exception $ex) {
            die("Error:".$ex->getMessage());
        }
        // see if the form is valid
        if (!$frmInstall->isValid($_POST)) {
            $this->_redirect($errorRedirect."&warning="
            .urlencode($this->view->translate->_("Invalid File Provided")));
        }
        // upload the file
        if(!$frmInstall->module->receive()) {
            $this->_redirect($errorRedirect."&warning="
            .urlencode($this->view->translate->_("Failed to upload the file")));
        }
        // get the new file location
        $location = $frmInstall->module->getFileName();
        // unzip the file
        $uncompressor = new Core_Config_Inst();
        $uncompressed=$uncompressor->uncompress($location);
        //process the install of the module
        if(!$uncompressed) {
            $this->_redirect($errorRedirect."&warning="
                .urlencode($this->view->translate->_("Failed to uncomress archive")));
        }
        // verify the file is a module
        if(!$uncompressor->verifyModule()) {
            $this->_redirect($errorRedirect."&warning="
            .urlencode($this->view->translate->_("File provided is not a valid Zend Module")));
        }
        // move the file to it's final destination
        $destination = APPLICATION_PATH.DIRECTORY_SEPARATOR
            .'modules'.DIRECTORY_SEPARATOR.$uncompressor->getName();
        rename($uncompressor->getPath(), $destination);
        //run install scripts
        $manifest = new Zend_Config_Xml($destination.DIRECTORY_SEPARATOR."Manifest.xml");

        /**
         * @todo add process to run manifest sql scripts
         */

        // mark the module as installed
        $mdlModules = new Model_ZFModules();
        $mdlModules->addMoudule($uncompressor->getName(), 'index', 'index');

        // find the model directory
        $modelDir = $destination.DIRECTORY_SEPARATOR."models".DIRECTORY_SEPARATOR;
        $dir = opendir($modelDir);
        // step through each modle and see if there is an install option
        echo "Running Model Installs<br />";
        while(false !== ($pth = readdir($dir))) {
            if($pth != '.' && $pth != '..' && $pth!='index.html') {
                $className = substr($uncompressor->getName(),1)."_Model_".substr($pth, 0,strlen($pth)-4);
                try {
                    $obj = new $className();
                    if(method_exists($obj, "createTable")) {
                        echo "Creating table:" . $className . "<br />";
                        $obj->createTable();
                    }
                } catch (Exception $ex) {
                    echo "Error processing model " . $className.": ".$ex->getMessage();
                }
            }
        }

        // send the user back to the list with success message
        $this->_redirect($errorRedirect."&message="
                .urlencode($this->view->translate->_("Successfully installed module ")
                .$uncompressor->getName()));
    }
    /**
     * uninstallAction
     *
     * uninstalls a module from the system
     */
    public function uninstallAction()
    {
        // location to send user to on an error
        $errorRedirect = JRoute::_($this->view->url(array(
                    'controller'=>$this->_name,
                    'action'=>'index',
                ),'administrator'));
        // get the params
        $name = $this->_getParam('name');
        if($name==NULL || strlen($name) <1) {
            $this->_redirect($errorRedirect."&error="
            .urlencode($this->view->translate->_("Error you muste specify a module to uninstall ").$ex->getMessage()));
        }
        // load the module data
        $mdlModules = new Model_ZFModules();
        $module = $mdlModules->getModuleByName($name);
        if($module==NULL) {
            $this->_redirect($errorRedirect."&error="
            .urlencode($this->view->translate->_("Error, could not find module to uninstall ").$name));
        }
        $sourceDir = APPLICATION_PATH.DIRECTORY_SEPARATOR
            .'modules'.DIRECTORY_SEPARATOR.$name;
        //load the manifest
        $manifest = new Zend_Config_Xml($sourceDir.DIRECTORY_SEPARATOR."Manifest.xml");

        // find the model directory
        $modelDir = $destination.DIRECTORY_SEPARATOR."models".DIRECTORY_SEPARATOR;
        $dir = opendir($modelDir);
        // step through each modle and see if there is an install option
        echo "Running Model Installs<br />";
        while(false !== ($pth = readdir($dir))) {
            if($pth != '.' && $pth != '..' && $pth!='index.html') {
                $className = $module."_Model_".substr($pth, 0,strlen($pth)-4);
                try {
                    $obj = new $className();
                    if(method_exists($obj, "createTable")) {
                        echo "Creating table:" . $className . "<br />";
                        $obj->createTable();
                    }
                } catch (Exception $ex) {
                    echo "Error processing model " . $className.": ".$ex->getMessage();
                }
            }
        }
        /**
         * @todo add code to run uninstall sql scripts
         */
        if(is_dir($sourceDir.DIRECTORY_SEPARATOR)) {
            $uncompressor = new Core_Config_Inst();
            $uncompressor->recursiveDelete($sourceDir.DIRECTORY_SEPARATOR);
        }
        // mark the module as uninstalled
        $mdlModules = new Model_ZFModules();
        $mdlModules->removeModule($name);
        $this->_redirect($errorRedirect."&message="
            .urlencode($this->view->translate->_("Successfully uninstalled module ").$name));
    }
}

