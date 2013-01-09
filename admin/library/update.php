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
 * @package Install
 * @link http://joomlazend.rbsolutions.us
 * @version $Id:$
 */

/**
 * Update
 *
 * Zend Framework update file that manages the updates to the core system
 *
 *
 * @author rbsolutions (rbsoultions.us@gmail.com)
 * @copyright (c) 2010 Red Black Tree LLC
 * @category ComZend
 * @package Install
 */
class update {
    /**
     * @var string stores the location of the update source file
     */
    protected $_sourceFolder="";
    /**
     * @var string stores the name of the configuration file to read for how to 
     * install
     */
    protected $_configFile = 'zend.xml';
    /**
     * @var NULL|SimpleXmlElement
     */
    protected $_xmlData = NULL;
    /**
     * @var sting stores the version number you want to update to
     */
    protected $_updateToVersion = "0.1.1a";
    /**
     * __construct
     *
     * initializes the class
     */
    public function __construct()
    {
        // load the folder location
        $config =& JFactory::getConfig();
        $this->_sourceFolder = $config->getValue('tmp_path').DIRECTORY_SEPARATOR
                .ZEND_COMPONENT_NAME.DIRECTORY_SEPARATOR;
    }
    /**
     * update
     * 
     * performs the actual update 
     */
    public function update($newVersion=NULL)
    {
        if($newVersion!=NULL) {
            $this->_updateToVersion = $newVersion;
        }
        $this->loadXML();
    }
    /**
     * loadXML
     *
     * loads the installer xml to
     */
    public function loadXML()
    {
        $this->_xmlData = simplexml_load_file($this->_sourceFolder.$this->_configFile);
        var_dump($this->_xmlData);
    }
}