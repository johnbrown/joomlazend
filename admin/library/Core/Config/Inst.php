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
 * Core_Config_Inst
 *
 * file management such as unzipping
 *
 *
 * @author rbsolutions (rbsoultions.us@gmail.com)
 * @copyright (c) 2010 Red Black Tree LLC
 * @category ComZend
 * @package Core
 * @subpackage Config
 */
class Core_Config_Inst {
    /**
     * @var string the extnsion of the file
     */
    protected $_extension = "zip";
    /**
     * @var string the name of the file
     */
    protected $_fileName = NULL;
    /**
     * @var string the pathe to the folder containing the files
     */
    protected $_basePath = NULL;
    /**
     * uncompressFile
     *
     * uncompresses the downloaded file
     *
     * @return boolean
     */
    public function uncompress($file, $basePath=NULL) {
        $this->parseName($file);
        if($basePath!=NULL) {
            $this->_basePath=$basePath;
        }
        chdir($this->_basePath);
        switch($this->_extension) {
            case "tar.gz":
                if(system('tar -zxvf ' . $this->_fileName . '.' . $this->_extension)) {
                    return true;
                } else {
                    echo ('Error, could not uncompress file, command has been disabled by host');
                    return false;
                }
                break;
            case "zip":
                $zip = new ZipArchive();
                if($zip->open($this->_basePath.$this->_fileName . '.' . $this->_extension)) {
                    //echo getcwd()."<hr />";
                    //echo $this->_fileName.".".$this->_extension."<hr />";
                    $zip->extractTo($this->_basePath.DIRECTORY_SEPARATOR.$this->_fileName.DIRECTORY_SEPARATOR);
                    $zip->close();
                    return true;
                } else if(system('unzip -zxvf ' . $this->_fileName . '.' . $this->_extension)) {
                    return true;
                } else {
                    echo ('Error, could not uncompress file, command has been disabled by host');
                    return false;
                }
                break;
            default:
                echo ("Error, invalid file extension:".$this->_extension);
                return false;
                break;
        }
    }
    /**
     * parseName
     *
     * parses the filename to extract the parts
     *
     * @param string $fileName
     */
    public function parseName($fileName)
    {
        $this->_basePath=substr($fileName,0,  strripos($fileName, '/'));
        $file = substr($fileName,strripos($fileName,'/'));
        $this->_fileName=substr($file,0,stripos($file,"."));
        $this->_extension=substr($file,stripos($file,".")+1);
    }
    /**
     * verifyModule
     * 
     * examine a module and determine if it is valid for install
     * 
     * @param string $folderName path to extracted module
     * @return boolean
     */
    public function verifyModule($folderName=NULL)
    {
        if($folderName!=NULL) {
            $this->_basePath=$folderName;
            $this->_fileName="";
        }
        // check to ensure the main deirecotry exists
        $basePath = $this->_basePath.$this->_fileName.DIRECTORY_SEPARATOR;
        if(!is_dir($this->_basePath.$this->_fileName)) {
            throw new Zend_Exception("Could not find directory: ".$basePath);
        }
        // check for reserved System Modules
        switch($this->_fileName) {
            case "Admin":
            case "Default":
            case "Update":
                throw new Zend_Exception("Error, you cannot install this module.  Invalid Name:".$this->_fileName);
                break;
            default:
                break;
        }

        // check for a manifest
        if(!is_file($basePath."Manifest.xml")) {
            throw new Zend_Exception("Module manifest does not exist:"
                    .$basePath."Manifest.xml");
        }
        // check for minimal proper structure
        if(!is_dir($basePath."controllers")||!is_dir($basePath.'views')) {
            throw new Zend_Exception("Invalid folder structure for a module");
        }

        $manifest = new Zend_Config_Xml($basePath.DIRECTORY_SEPARATOR."Manifest.xml");
        //var_dump($manifest);
        if($this->verifyFolder($manifest->files->folder,$this->_basePath)) {
            return true;
        }
        return false;
    }
    /**
     * verifyFolder
     *
     * verifys the manifest's file structure and throw errors if they pop up
     *
     * @param Zend_Config $inputFolder
     * @param string $basePath
     * @return boolean
     */
    public function verifyFolder(Zend_Config $inputFolder, $basePath="")
    {
        $newPath = $basePath.DIRECTORY_SEPARATOR.$inputFolder->name;
        if(!is_dir($newPath)) {
            throw new Zend_Exception("Error, object in manifest not found:".$newPath);
        }
        foreach ($inputFolder as $file) {
            if(is_string($file) && $file != $inputFolder->name) {
                if(!is_file($newPath.DIRECTORY_SEPARATOR.$file)) {
                    throw new Zend_Exception("Error, object in manifest not found:".$newPath.DIRECTORY_SEPARATOR.$file);
                }
            } elseif($file instanceof Zend_Config) {
                return $this->verifyFolder($file,$basePath.DIRECTORY_SEPARATOR.$inputFolder->name);
            }
        }
        return true;
    }
    /**
     * getPath
     * 
     * gets the current  path
     * 
     * @return string 
     */
    public function getPath()
    {
        return $this->_basePath.DIRECTORY_SEPARATOR.$this->_fileName;
    }
    /**
     * getName
     * 
     * gets the name from the object
     * 
     * @return string
     */
    public function getName()
    {
        return $this->_fileName;
    }
    /**
     * recursiveDelete
     *
     * deletes paths
     *
     * @param <type> $path
     * @return <type>
     */
    public function recursiveDelete($path)
    {
        if(is_file($path)) {
            return @unlink($path);
        } else if(is_dir($path)) {
            $dir = opendir($path);
            while(false !== ($pth = readdir($dir))) {
                if($pth != '.' && $pth != '..') {
                    $this->recursiveDelete($pth);
                }
            }
            closedir($dir);
            return @rmdir($path);
        }
    }
}

