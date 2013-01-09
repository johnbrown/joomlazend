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
 * ConfigController
 *
 * Controlls the configuration for the Zend component
 *
 *
 * @author rbsolutions (rbsoultions.us@gmail.com)
 * @copyright (c) 2010 Red Black Tree LLC
 * @category ComZend
 * @package Admin
 * @subpackage Controller
 */
class Admin_ConfigController extends Core_Controller_Action
{
    /**
     * @var string the title for the current controller
     */
    protected $_title = "Zend Framework Configuration";
    /**
     * @var string the destination directory
     */
    protected $_destDir = NULL;
    /**
     * @var string location of the admin component directory
     */
    protected $_adminDir = NULL;
    /**
     * @var string location of the public component directory
     */
    protected $_publicDir = NULL;
    /**
     * init
     *
     * initializes the controller
     **/
    public function init()
    {
        $this->_destDir = ROOT_DIR . DIRECTORY_SEPARATOR . "build";
        if(!is_dir($this->_destDir)) {
            mkdir($this->_destDir);
        }

        $this->_publicDir = ROOT_DIR.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR
            . '..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR
            . ZEND_COMPONENT_NAME;
        $this->_adminDir = ROOT_DIR;
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
               // top menu options
                JToolBarHelper::title($this->view->translate->_($this->_title));
                JToolBarHelper::preferences( ZEND_COMPONENT_NAME );
                // main menu options
                $menuItems = array();
                $count = 0;
                // add menu Items
                $menuItems[$count++] = array(
                    'link'=>JRoute::_($this->view->url(array(
                            'module'=>'Admin',
                            'controller'=>'config',
                            'action'=>'compile',
                        ),'administrator',true)),
                    'icon'=>'header'.DIRECTORY_SEPARATOR.'icon-48-generic.png',
                    'text'=>$this->view->translate->_('Compile'),
                );

                $menuItems[$count++] = array(
                    'link'=>JRoute::_($this->view->url(array(
                            'module'=>'Update',
                            'controller'=>'index',
                            'action'=>'check', 
                        ),'administrator',true)),
                    'icon'=>'header'.DIRECTORY_SEPARATOR.'icon-48-install.png',
                    'text'=>$this->view->translate->_('Check for Updates'),
                );

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
        } catch(exception $ex) {
            echo $ex->__toString();
        }
    }
    
    /**
     * compileAction
     * 
     * compiles the component for distribution
     */
    public function compileAction()
    {
        if($this->_application->isAdmin()) {
               // top menu options
                JToolBarHelper::title('Compiling '.ZEND_COMPONENT_NAME);
        }
        $this->view->results.= $this->view->translate->_("Building Directory Structure")."<br />";
        // create the admin directory
        if(!is_dir($this->_destDir.DIRECTORY_SEPARATOR.'admin')) {
             mkdir($this->_destDir.DIRECTORY_SEPARATOR.'admin');
        }
        // move the files into the admin directory
        $this->recursiveCopy($this->_adminDir,
                $this->_destDir.DIRECTORY_SEPARATOR.'admin');
        // create the site directory
        if(!is_dir($this->_destDir.DIRECTORY_SEPARATOR.'site')) {
             mkdir($this->_destDir.DIRECTORY_SEPARATOR.'site');
        }
        // move the files into the admin directory
        $this->recursiveCopy($this->_publicDir,
                $this->_destDir.DIRECTORY_SEPARATOR.'site');

        // create the xml file
        $this->createxmlAction();

        $params = &JComponentHelper::getParams(ZEND_COMPONENT_NAME);
        $this->view->paramArray = $params->toArray();
        $currentVersion = $this->view->paramArray['version'];

        $config =& JFactory::getConfig();
        $tmpPath = $config->getValue('tmp_path');

        if (!$this->compressFolder($this->_destDir.DIRECTORY_SEPARATOR,
                $tmpPath.DIRECTORY_SEPARATOR.ZEND_COMPONENT_NAME."_".$currentVersion.'.zip')) {
            $this->view->results.= "Failed to create Zip file:".$this->_publicDir.DIRECTORY_SEPARATOR
                    . ZEND_COMPONENT_NAME."_".$currentVersion.'.zip From folder:' . $this->_destDir.DIRECTORY_SEPARATOR."<br />";
        } else {
            // delete the build directory
            $this->recursiveDelete($this->_destDir);
        }
        $this->view->results.= $this->view->translate->_("Done, your component is now available at")
                ." <a href='/tmp/"
            .ZEND_COMPONENT_NAME."_".$currentVersion.".zip'>/tmp/"
                .ZEND_COMPONENT_NAME."_".$currentVersion.".zip</a>";
    }
    /**
     * compressFolder
     *
     * creates a zip file from a directory
     *
     * @param string $source
     * @param string $fileName
     * @return boolean
     */
    public function compressFolder($source, $fileName, $overwrite = true)
    {
        if(file_exists($fileName)&&$overwrite) {
            unlink($fileName);
        }
        // use php if possible
        if(extension_loaded('zip')!==true) {
            if(chdir($source)) {
                ob_start();
                $result =system('zip -r ' . $fileName . ' ./');
                $ZipData = ob_get_clean();
                if($result) {
                    return true;
                } else {
                    return false;
                }
            } else {
                echo ('Error, could not compress file, command has been disabled by host');
                return false;
            }
        } elseif(file_exists($source)) {
            $zip = new ZipArchive();
            if($zip->open($fileName,ZIPARCHIVE::CREATE)===true) {
                $source = realpath($source);
                if(is_dir($source)) {
                    $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source), RecursiveIteratorIterator::SELF_FIRST);
                    foreach($files as $file) {
                        $flle = realpath($file);
                        if(is_dir($file)) {
                            $zip->addEmptyDir(str_replace($source."/",'',$file.'/'));
                        } else if (is_file($file)) {
                            $zip->addFromString(str_replace($source.'/', '', $file), file_get_contents($file));
                        }
                    }
                } else if (is_file($source)) {
                    $zip->addFromString(basename($source), file_get_contents($srouce));
                }
            }
            return $zip->close();
        }
        return false;
    }
    /**
     * createxmlAction
     *
     * creates the installer xml based on the current state of the system.
     */
    public function createxmlAction()
    {
        $this->view->results.= $this->view->translate->_("Building XML")."<br />";
        $config = new Core_Config_XML();
        $config->writeConfig($this->_destDir . DIRECTORY_SEPARATOR . 'zend.xml');
        $mdlParams = new Model_Components();
        $params = $mdlParams->getParams(ZEND_COMPONENT_NAME);
        if(!isset($params['version'])) {
            jError::raiseError(500,'Error param version does not exist');
            return;
        }
        $config->writeUpdate(APPLICATION_PATH.DIRECTORY_SEPARATOR.'..'
                    .DIRECTORY_SEPARATOR.'library'.DIRECTORY_SEPARATOR.'versions.xml',
                $params['version'], '/');
    }
    /**
     * recursiveCopy
     *
     * recursivily copies directories
     * 
     * @param srting $src
     * @param string $dest
     */
    public function recursiveCopy($src, $dest)
    {
        if(is_dir($src)) {
            $dir = opendir($src);
            if(!is_dir($dest)) {
                @mkdir($dest);
            }
            while(false !== ($file = readdir($dir))) {
                if($file !='.' && $file !='..') {
                    if(is_dir($src.DIRECTORY_SEPARATOR.$file)) {
                        if($file!='build' && $file != 'Zend' && $file != 'ZendX') {
                            $this->recursiveCopy($src.DIRECTORY_SEPARATOR.$file,$dest.DIRECTORY_SEPARATOR.$file);
                        }
                    } else {
                        if($file != ZEND_COMPONENT_NAME.'.zip') {
                            //$this->view->results.= "Copying File:" . $src.DIRECTORY_SEPARATOR.$file
                            //. " TO " . $dest.DIRECTORY_SEPARATOR.$file."<br />";
                            copy($src.DIRECTORY_SEPARATOR.$file,$dest.DIRECTORY_SEPARATOR.$file);
                        }
                    }
                }
            }
            closedir($dir);
        } else if(is_file($src)) {
            copy($src,$dest);
        }
    }
    /**
     * recursiveDelete
     *
     * deletes paths
     *
     * @param string $path
     * @return <type>
     */
    public function recursiveDelete($path)
    {
        if(is_file($path)) {
            return @unlink($path);
        } else if(is_dir($path)) {
            $scan = glob(rtrim($path,'/').'/*');
            foreach($scan as $index=>$pth) {
                $this->recursiveDelete($pth);
            }
            return @rmdir($path);
        }
    }
    /**
     * installAction
     * 
     * the installZend installer class will redirect to this action after
     * it has installed the zend base information
     */
    public function installAction()
    {
        // find the model directory
        $modelDir = APPLICATION_PATH.DIRECTORY_SEPARATOR."models".DIRECTORY_SEPARATOR;
        $dir = opendir($modelDir);
        // step through each modle and see if there is an install option
        echo "Running Model Installs<br />";
        while(false !== ($pth = readdir($dir))) {
            if($pth != '.' && $pth != '..' && $pth!='index.html') {
                $className = "Model_".substr($pth, 0,strlen($pth)-4);
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
        echo "<h2>Successfully installed</h2>";
    }
    /**
     * uninstallAction
     * 
     * handles the uninstall action
     */
    public function uninstallAction()
    {
        // find the model directory
        $modelDir = APPLICATION_PATH.DIRECTORY_SEPARATOR."models".DIRECTORY_SEPARATOR;
        $dir = opendir($modelDir);
        // step through each modle and see if there is an install option
        echo "Running Model Uninstalls<br />";
        while(false !== ($pth = readdir($dir))) {
            if($pth != '.' && $pth != '..' && $pth!='index.html') {
                $className = "Model_".substr($pth, 0,strlen($pth)-4);
                try {
                    $obj = new $className();
                    if(method_exists($obj, "removeTable")) {
                        echo "Removing table:" . $className . "<br />";
                        $obj->removeTable();
                    }
                } catch (Exception $ex) {
                    echo "Error processing model " . $className.": ".$ex->getMessage();
                }
            }
        }
        echo "<h2>Successfully uninstalled</h2>";
    }
}

