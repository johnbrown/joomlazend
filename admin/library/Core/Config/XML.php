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
 * Core_Config_XML
 *
 * builds the Joomla install xml during compling
 *
 *
 * @author rbsolutions (rbsoultions.us@gmail.com)
 * @copyright (c) 2010 Red Black Tree LLC
 * @category ComZend
 * @package Core
 * @subpackage Config
 */
class Core_Config_XML {
    /**
     * @var DOMDocument the docuemnt
     */
    protected $_xmlDoc = NULL;
    /* 
     * @var string joomla version
     */
    protected $_JoomlaVersion = '1.5.1';
    /**
     * @var string current version of the zend for joomla component
     */
    protected $_version = '0.1.2a';
    /**
     * @var string the name of the component
     */
    protected $_name = "zend";
    /**
     * @var string the name of the author
     */
    protected $_author = "John Brown";
    /**
     * @var string email address of the author
     */
    protected $_authorEmail = 'rbsolutions.us@gmail.com';
    /**
     * @var string url of the author's website
     */
    protected $_authorUrl='http://www.redblacktreellc.com';
    /**
     * @var string the license type
     */
    protected $_license='GNU/GPL';
    /**
     * @var string description of the component
     */
    protected $_description = '';
    /**
     * @var string the public path
     */
    protected $_publicDir='';
    /**
     * @var string the name of the output file
     */
    protected $_fileName = '';
    /**
     * __construct
     *
     * loads the defaults
     */
    public function  __construct()
    {
        $this->_publicDir = ('components'.DIRECTORY_SEPARATOR.ZEND_COMPONENT_NAME);
        $params = &JComponentHelper::getParams(ZEND_COMPONENT_NAME);
        $paramArray = $params->toArray();
        $this->_version = $paramArray['version'];
    }
    /**
     * writeUpdate
     *
     * writes an update file to the client
     */
    public function writeUpdate($filename, $versionNum, $notes='', $type='development')
    {
        $this->_xmlDoc = simplexml_load_file($filename);
        $version = $this->_xmlDoc->versions->addChild('version');
        $version->addAttribute('id', $versionNum);
        $version->addAttribute('type', $type);
        $version->addChild('file','/components/'.ZEND_COMPONENT_NAME.'/'
                .ZEND_COMPONENT_NAME.'_'.$versionNum.'.zip');
        $version->addChild('notes',$notes);
        $this->_xmlDoc->asXML($filename);
    }
    /**
     * readUpdate
     * 
     * reads the update XML file and returns the results as an array
     * 
     * @param string $URL
     * @return array
     */
    public function readUpdate($URL)
    {
        $xml = simplexml_load_file($URL);
        $results = array();
        $count = 0;
        foreach($xml->versions->version as $version) {
            $attributes = $version->attributes();
            $results[$count] = array(
                'id'=>((string)$attributes->id),
                'type'=>((string)$attributes->type),
                'url'=>$version->file,
                'notes'=>$version->notes,
            );
            
            $count +=1;
        }
        return $results;
    }
    /**
     * calculates the next version id
     */
    public function getNextVersionId($major=false, $minor=false, $build=true, $stable=false)
    {
        $mdlParams = new Model_Components();
        $params = $mdlParams->getParams(ZEND_COMPONENT_NAME);
        if(!isset($params['version'])) {
            jError::raiseError(500,'Error param version does not exist');
            return;
        }
        $currentVersion = explode('.',$params['version']);
        if($major) {
            $currentVersion[0]+=1;
        }
        if($minor){
            $currentVersion[1]+=1;
        }
        if($build){
            $tmp = (int)substr($currentVersion[2],0,(strlen($currentVersion[2])-1));
            $sta = "a";
            $tmp +=1;
            $currentVersion[2]=$tmp.$sta;
        }
        if($stable) {
            $tmp = substr($currentVersion[2],0,(strlen($currentVersion[2])-1));
            $sta = "a";
            $currentVersion[2]=$tmp.$sta;
        }
        $this->_version = implode(".", $currentVersion);
        // update the curren session
        $jparams = &JComponentHelper::getParams(ZEND_COMPONENT_NAME);
        $jparams->set('version',$this->_version);
        $params['version'] = $this->_version;
        // update the database
        $mdlParams->setParams(ZEND_COMPONENT_NAME,$params);
        return $this->_version;
    }
    /**
     * writeConfig
     *
     * create a component install xml file
     *
     * @param string $fileName the name of the output file
     */
    public function writeConfig($fileName)
    {
        $doctype = DOMImplementation::createDocumentType('install',''
                ,"http://dev.joomla.org/xml/1.5/component-install.dtd");
        $this->_xmlDoc = DOMImplementation::createDocument("1.0","",$doctype);
        $this->_xmlDoc->encoding = 'utf-8';
        $this->_xmlDoc->formatOutput=true;
        $this->_fileName = $fileName;
        // create the output
        $install = $this->_xmlDoc->createElement('extension');
        $install->setAttribute('type', 'component');
        $install->setAttribute('version', $this->_JoomlaVersion);

        $this->addNewElement('name', $this->_name, $install);
        $this->addNewElement('creationDate', date('m-d-Y'), $install);
        $this->addNewElement('author', $this->_author, $install);
        $this->addNewElement('authorEmail', $this->_authorEmail, $install);
        $this->addNewElement('authorUrl', $this->_authorUrl, $install);
        $this->addNewElement('copyright', '(c) ' . date("Y") . ' All rights reserved.', $install);
        $this->addNewElement('license', $this->_license, $install);
        $this->addNewElement('version', $this->_JoomlaVersion, $install);
        $this->addNewElement('zmjoomlaVersion', $this->getNextVersionId(), $install);
        $this->addNewElement('description', $this->_description, $install);
        $this->addNewElement('installfile', 'library/install.php',$install);
        $this->addNewElement('uninstallfile', 'library/uninstall.php',$install);

        $params = $this->_xmlDoc->createElement('params');
        $version = $this->_xmlDoc->createElement('param');
        $version->setAttribute('name','version');
        $version->setAttribute('default',$this->_version);
        $params->appendChild($version);
        $install->appendChild($params);
        
        // process the public files
        $publicFiles = $this->_xmlDoc->createElement('files');
        $publicFiles->setAttribute('folder', 'site');
        $this->processPublicFiles('',$publicFiles);
        $install->appendChild($publicFiles);

        // process teh administration
        $administration = $this->_xmlDoc->createElement('administration');
        // create the admin menu
        $adminMenu = $this->_xmlDoc->createElement('menu', 'Zend Module');
        $adminMenu->setAttribute('link', 'option=com_zend&zmodule=Admin');
        $administration->appendChild($adminMenu);

        // process the administration files
        $adminFiles = $this->_xmlDoc->createElement('files');
        $adminFiles->setAttribute('folder', 'admin');
        $this->processAdminFiles('', $adminFiles);
        $administration->appendChild($adminFiles);
        $install->appendChild($administration);

        // sql installer
        $sqlinstall = $this->_xmlDoc->createElement('install');
        $sqlInst = $this->_xmlDoc->createElement('sql');
        $installFile = $this->_xmlDoc->createElement('file','library/install.sql');
        $installFile->setAttribute('driver', 'mysql');
        $installFile->setAttribute('charset', 'utf8');
        $sqlInst->appendChild($installFile);
        $sqlinstall->appendChild($sqlInst);
        $install->appendChild($sqlinstall);

        // sql uninstaller
        $sqluninstall = $this->_xmlDoc->createElement('uninstall');
        $sqlUnInst = $this->_xmlDoc->createElement('sql');
        $uninstallFile = $this->_xmlDoc->createElement('file','library/uninstall.sql');
        $uninstallFile->setAttribute('driver', 'mysql');
        $uninstallFile->setAttribute('charset', 'utf8');
        $sqlUnInst->appendChild($uninstallFile);
        $sqluninstall->appendChild($sqlUnInst);
        $install->appendChild($sqluninstall);


        // add the root node
        $this->_xmlDoc->appendChild($install);
        $this->_xmlDoc->save($this->_fileName);
//        echo $this->_xmlDoc->saveXML();
//        die();
    }
    /**
     * processPublicFiles
     *
     * collects a list of all the public files
     *
     * @param string $directory
     * @param DomElement $parent
     */
    public function processPublicFiles($directory, $parent)
    {
        $handle = openDir(ROOT_DIR.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR
                .'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR
                .$this->_publicDir.DIRECTORY_SEPARATOR.$directory);
        while(false!== ($readdir = readdir($handle))) {
            if($readdir!='.' && $readdir!='..') {
                if(strlen($directory) > 0) {
                    $path = $directory.DIRECTORY_SEPARATOR;
                } else {
                    $path = '';
                }
                $path.=$readdir;
                if(is_file(ROOT_DIR.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR
                .'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR
                .$this->_publicDir.DIRECTORY_SEPARATOR.$path)) {
                    if($path != ZEND_COMPONENT_NAME.'.zip') {
                        $this->addNewElement('filename', $path, $parent);
                    }
                } else {
                    $this->processPublicFiles($path, $parent);
                }
            }
        }
    }
    /**
     * collects a list of all the non-library admin application files
     *
     * collects a list of all the public files
     *
     * @param string $directory
     * @param DomElement $parent
     * @param <type> $directory
     * @param <type> $parent
     */
    public function processAdminFiles($directory, $parent) {
        $handle = openDir(ROOT_DIR.DIRECTORY_SEPARATOR.$directory);
        while(false!== ($readdir = readdir($handle))) {
            if($readdir!='.' && $readdir!='..') {
                if(strlen($directory) > 0) {
                    $path = $directory.DIRECTORY_SEPARATOR;
                } else {
                    $path = '';
                }
                $path .=$readdir;
                if(is_file(ROOT_DIR.DIRECTORY_SEPARATOR.$path)) {
                    $this->addNewElement('filename', $path, $parent);
                } else if($path!='library'.DIRECTORY_SEPARATOR.'Zend'
                        && $path != 'library'.DIRECTORY_SEPARATOR.'ZendX'
                        && $path != 'build'){
                    //echo $path."<hr />";
                    $this->processAdminFiles($path, $parent);
                }
            }
        }
    }
    /**
     * addNewElement
     *
     * creates and adds a new element to the config xml
     *
     * @param string $name
     * @param string $value
     * @param mixed $parent
     * @param array $attributes
     */
    public function addNewElement($name, $value, $parent=NULL, array $attributes = array())
    {
        $element = $this->createElement($name, $value, $attributes);
        if($parent== NULL) {
            $parent = $this->_xmlDoc;
        }
        $this->addElement($element, $parent);
    }
    /**
     * createElement
     *
     * creates a new element 
     *
     * @param string $name
     * @param string $value
     * @param array $attributes
     */
    public function createElement($name, $value="", array $attributes = array())
    {
        $element = $this->_xmlDoc->createElement($name);
        foreach($attributes as $attr=>$val) {
            $element->setAttribute($attr, $val);
        }
        if(strlen($value)>0) {
            $textNode = $this->_xmlDoc->createTextNode($value);
            $element->appendChild($textNode);
        }
        return $element;
    }
    /**
     * addElement
     *
     * adds an element to the dom
     *
     * @param domElement $element
     * @param mixed $parent the parent elmenet or null
     */
    public function addElement($element, $parent = NULL)
    {
        if($parent != NULL) {
            $parent->appendChild($element);
        } else {
            $this->_xmlDoc->appendChild($element);
        }
    }
}

