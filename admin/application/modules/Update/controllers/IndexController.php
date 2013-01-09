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
 * @package Update
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
 * @package Update
 * @subpackage Controller
 */
class Update_IndexController extends Core_Controller_Action{
    /**
     * @var string the server to contact for updates
     */
    protected $_updateServer = "http://zfjoomla.googlecode.com";
    /**
     * @var string the server to look at for newer versions
     */
    protected $_rootServer = "http://zfjoomla.rbsolutions.org";
    /**
     * @var string the url for updates to be read from
     */
    protected $_updateURL = '/administrator/components/com_zend/library/versions.xml';
    /**
     * @var string the title for the current controller
     */
    protected $_title = "Zend Framework Update";
    /**
     * @var DOMDocument the docuemnt
     */
    protected $_xmlDoc = NULL;
    /**
     * checkAction
     * 
     * checks the default xml for any needed upgrades
     */
    public function checkAction()
    {
        try {
            $xml = new Core_Config_XML();
            $values = $xml->readUpdate($this->_rootServer.$this->_updateURL);
            $this->view->maxUpdate = $values[sizeof($values)-1];
            if(isset($this->view->maxUpdate['id'])) {
                $maxVersion = explode(".", $this->view->maxUpdate['id']);
                $params = &JComponentHelper::getParams(ZEND_COMPONENT_NAME);
                $this->view->paramArray = $params->toArray();
                $currentVersion = explode('.',$this->view->paramArray['version']);
                $count = 0;
                $this->view->upgrade = false;
                foreach ($currentVersion as $v) {
                    if ($maxVersion[$count] > $v) {
                        $this->view->upgrade=true;
                    }
                    $count ++;
                }
            }
        } catch(exception $ex) {
            echo $ex->__toString();
        }
    }
    /**
     * findNextVersion
     *
     * searches for the next available id
     * 
     * @param string $currentVersion 
     */
    private function findNextVersion($currentVersion)
    {
        $xml = new Core_Config_XML();
        $values = $xml->readUpdate($this->_updateServer.$this->_updateURL);
        foreach ($values as $value) {
            if(isset($value['id'])) {
                $versionArray = explode(".",$value['id']);
                $count = 0;
                foreach ($versionArray as $version) {
                    if($version > $currentVersion[$count]) {
                        return $value;
                    }
                    $count ++;
                }
            }
        }
        return NULL;
    }
    /**
     * upgradeAction
     *
     * performs an upgrade from the currernt installed version to the next
     * available version
     */
    public function upgradeAction()
    {
        $params = &JComponentHelper::getParams(ZEND_COMPONENT_NAME);
        $this->view->paramArray = $params->toArray();
        $currentVersion = explode('.',$this->view->paramArray['version']);
        $this->view->nextVersion = $this->findNextVersion($currentVersion);
        if($this->view->nextVersion != NULL) {
            echo "Downloading file:<br />";
            flush();
            try {
                echo $this->_updateServer.$this->view->nextVersion['url'][0]."<br />";
                flush();
                $config =& JFactory::getConfig();
                $tmpPath = $config->getValue('tmp_path');
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $this->_updateServer.$this->view->nextVersion['url'][0]);
                $destFile = $tmpPath.DIRECTORY_SEPARATOR
                        .ZEND_COMPONENT_NAME.'_'.$this->view->nextVersion['id'].'.zip';
                echo 'To '.$destFile.'<br />';
                $fp = fopen($destFile,'w');
                curl_setopt($ch, CURLOPT_FILE, $fp);
                curl_exec($ch);
                curl_close($ch);
                fclose($fp);
                echo "Done Downloading<br />";
                flush();
                mkdir($tmpPath.DIRECTORY_SEPARATOR.ZEND_COMPONENT_NAME);
                system('unzip -q '.$destFile . ' -d ' .$tmpPath.DIRECTORY_SEPARATOR.ZEND_COMPONENT_NAME.DIRECTORY_SEPARATOR);
                echo "Starting Updater<br />";
                flush();
                // load the newly downloaded update file and run it
                require_once($tmpPath.DIRECTORY_SEPARATOR.ZEND_COMPONENT_NAME
                        .DIRECTORY_SEPARATOR.'admin'.DIRECTORY_SEPARATOR.'library'.DIRECTORY_SEPARATOR.'update.php');
                $update = new update();
                $update->update($this->view->nextVersion['id']);
                echo "Cleaning Up<br />";
                flush(); 
                unlink($destFile);
                echo "Done";
            } catch(exception $ex) {
                echo "Error, could not download version ".$this->view->nextVersion['id'];
            }
        } else {
            echo "Error, no new updates available";
        }
    }
    /**
     * installAction
     *
     * runs the install scripts necessary to install the core systems.
     */
    public function installAction()
    {
        // check to see if the modules table exists, if not create it
        $mdlModules = new Model_ZFModules();
        $mdlModules->createTable();
    }
    /**
     * uninstallAction
     *
     * runss the uninstallscripts to remove the cores systems.
     */
    public function uninstallAction()
    {
        // remove the moudles table
        $mdlModules = new Model_ZFModules();
        $mdlModules->removeTable();
    }
}

