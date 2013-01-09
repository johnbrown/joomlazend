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
defined("ZEND_COMPONENT_NAME")
        || define('ZEND_COMPONENT_NAME','com_zend');
define ('_VALID_MOS',true);
/**
 * installZend
 *
 * Default default installer for the Joomla module
 *
 *
 * @author rbsolutions (rbsoultions.us@gmail.com)
 * @copyright (c) 2010 Red Black Tree LLC
 * @category ComZend
 * @package Install
 */
class installZend {
    /**
     * @var string the version number to download
     */
    protected $_zendVersion="1.11.0";
    /**
     * @var string the base url for downloading the framework
     */
    protected $_baseServerUrl = "http://downloads.zend.com/framework/";
    /**
     * @var string the extension of the file downloaded
     */
    protected $_extension = '';
    /**
     * @var string the url to download the framework from
     */
    protected $_serverUrl = '';
    /**
     * @var string the base path for the current folder
     */
    protected $_basePath = '';
    /**
     * @var string the name for the file
     */
    protected $_fileName = 'zf';
    /**
     * __construct
     *
     * configures class variables
     *
     * @param string $extension the extension to download (tar.bz for linux, zip for windows)
     */
    public function  __construct($extension = "zip")
    {
        $this->_extension = $extension;
        $this->_basePath =  dirname($_SERVER[SCRIPT_FILENAME]);
        $this->_serverUrl = $this->_baseServerUrl.$this->_zendVersion
                ."/ZendFramework-".$this->_zendVersion.".".$this->_extension;
        chdir($this->_basePath.DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR.ZEND_COMPONENT_NAME);
        if($this->downloadZend()) {
            ob_start();
            require_once $this->_basePath.DIRECTORY_SEPARATOR."components"
                    .DIRECTORY_SEPARATOR.ZEND_COMPONENT_NAME.DIRECTORY_SEPARATOR
                    .'library'.DIRECTORY_SEPARATOR.'Core'.DIRECTORY_SEPARATOR
                    .'Config'.DIRECTORY_SEPARATOR.'Inst.php';
            $uncompressor = new Core_Config_Inst();
            $uncompressed = $uncompressor->uncompress($this->_fileName . '.' . $this->_extension, 
                    $this->_basePath.DIRECTORY_SEPARATOR."components"
                    .DIRECTORY_SEPARATOR.ZEND_COMPONENT_NAME.DIRECTORY_SEPARATOR);
            $output = ob_get_clean();
            echo "<blockquote class='output'>".$output."</blockquote>";
            if($uncompressed) {
                // clean up original file
                unlink($this->_fileName . '.' . $this->_extension);
                $moved = $this->moveFolders();
            }
        }
        if($moved) {
          echo "<p>Install Successful.</p>";
        } else {
          echo "<p>Install Failed.</p>";
        }
        chdir($this->_basePath);
        $installdest = "/administrator/index.php?option=com_zend&zmodule=Update&zcontroller=index&zaction=install";
        echo "<script type='text\javascript'>window.location='" . $installdest . "';</script>";
    }
    /**
     * moveFolders
     *
     * moves the Zend and ZendX folders from their download path to their final
     * location
     */
    public function moveFolders()
    {
        try {
            flush();
            // place the ZendX directory
            // start by checking for an existing directrory
            if(is_dir($this->_basePath.DIRECTORY_SEPARATOR."components"
                    .DIRECTORY_SEPARATOR.ZEND_COMPONENT_NAME.DIRECTORY_SEPARATOR
                    ."zf".DIRECTORY_SEPARATOR
                    .'library'.DIRECTORY_SEPARATOR.'Zend')) {
                echo "<p>Removing Zend Directory</p>";
                $this->recursiveDelete($this->_basePath.DIRECTORY_SEPARATOR
                        ."components".DIRECTORY_SEPARATOR.ZEND_COMPONENT_NAME
                        .DIRECTORY_SEPARATOR."zf".DIRECTORY_SEPARATOR
                        .'library'.DIRECTORY_SEPARATOR.'Zend');
            }
            // move the folder
            echo "<p>Installing Zend Directory</p>";
            flush();
            if(rename($this->_basePath.DIRECTORY_SEPARATOR."components"
                    .DIRECTORY_SEPARATOR.ZEND_COMPONENT_NAME.DIRECTORY_SEPARATOR."zf"
                    .DIRECTORY_SEPARATOR.'ZendFramework-'
                    .$this->_zendVersion.DIRECTORY_SEPARATOR.'library'.DIRECTORY_SEPARATOR.'Zend',
                    $this->_basePath.DIRECTORY_SEPARATOR."components"
                    .DIRECTORY_SEPARATOR.ZEND_COMPONENT_NAME.DIRECTORY_SEPARATOR.'library'.DIRECTORY_SEPARATOR.'Zend')) {
                echo "Success";
            }
            flush();
            // place the ZendX directory
            // start by checking for an existing directrory
            if(is_dir($this->_basePath.DIRECTORY_SEPARATOR."components"
                    .DIRECTORY_SEPARATOR.ZEND_COMPONENT_NAME.DIRECTORY_SEPARATOR
                    ."zf".DIRECTORY_SEPARATOR
                    .'library'.DIRECTORY_SEPARATOR.'ZendX')) {
                echo "<p>Removing ZendX Directory</p>";
                $this->recursiveDelete($this->_basePath.DIRECTORY_SEPARATOR
                        ."components".DIRECTORY_SEPARATOR.ZEND_COMPONENT_NAME
                        .DIRECTORY_SEPARATOR."zf"
                        .DIRECTORY_SEPARATOR.'library'.DIRECTORY_SEPARATOR.'ZendX');
            }
            flush();
            // move the folder
            echo "<p>Installing ZendX Directory</p>";
            flush();
            if(rename($this->_basePath.DIRECTORY_SEPARATOR."components"
                    .DIRECTORY_SEPARATOR.ZEND_COMPONENT_NAME.DIRECTORY_SEPARATOR."zf"
                    .DIRECTORY_SEPARATOR.'ZendFramework-'
                    . $this->_zendVersion .DIRECTORY_SEPARATOR.'extras'
                    .DIRECTORY_SEPARATOR.'library'.DIRECTORY_SEPARATOR.'ZendX',
                    $this->_basePath.DIRECTORY_SEPARATOR."components"
                    .DIRECTORY_SEPARATOR.ZEND_COMPONENT_NAME.DIRECTORY_SEPARATOR
                    .'library'.DIRECTORY_SEPARATOR.'ZendX')) {
                echo "Success";
            }
            flush();
            // clean up the remaining files
            if(is_dir($this->_basePath.DIRECTORY_SEPARATOR."components"
                    .DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR."zf".ZEND_COMPONENT_NAME.DIRECTORY_SEPARATOR
                    .'ZendFramework-'.$this->_zendVersion)) {
                echo "<p>Removing Temp Files</p>";
                $this->recursiveDelete($this->_basePath.DIRECTORY_SEPARATOR
                        ."components".DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR."zf".ZEND_COMPONENT_NAME
                        .DIRECTORY_SEPARATOR.'ZendFramework-'.$this->_zendVersion);
            }
            flush();
            // redirect the user to the final installer location
            $dest="/administrator/index.php?option=com_zend&zmodule=Admin&zcontroller=config&zaction=install";
            ?><script type="text/javascript">
                window.location='<?php echo $dest;?>';
            </script>
            <p>If you are not already redirected, please
                <a href="<?php echo $dest;?>">
                    click here to continue
                </a>.</p><?php
        } catch (exception $ex) {
            echo $ex;
            return false;
        }
        return true;
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
    /**
     * downloadZend
     * 
     * download the zend framework
     */
    public function downloadZend()
    {
        echo "<p>Downloading file</p>";
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->_serverUrl);
            $fp = fopen($this->_fileName.'.' . $this->_extension,'w');
            curl_setopt($ch, CURLOPT_FILE, $fp);
            curl_exec($ch);
            curl_close($ch);
            fclose($fp);
        } catch(exception $ex) {
            echo "Error, could not download the Zend Frameworkk";
            return false;
        }
        return true;
    }
}



