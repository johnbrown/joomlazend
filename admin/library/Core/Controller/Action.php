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
defined('_JEXEC') or 
    die('Direct Access to this location is not allowed');
/**
 * Core_Controller_Action
 *
 * Default Template class for Zend for Joomla Controllers
 *
 *
 * @author rbsolutions (rbsoultions.us@gmail.com)
 * @copyright (c) 2010 Red Black Tree LLC
 * @category ComZend
 * @package Core
 * @subpackage Controller
 */
class Core_Controller_Action extends Zend_Controller_Action
{
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
    protected $_title = "Zend for Joomla";
    /**
     * @return JApplication stores the current joomla application
     */
    protected $_application=NULL;
    /**
     * @var Zend_Translate|NULL the translattion object
     */
    protected $_translate=NULL;
    /**
     * @var string the default language
     */
    protected $_defaultLanguage='en_US';
    /**
     * @var obj|NULL
     */
    protected $_addParams=NULL;
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
        // add the translator
        $this->_translate = new Zend_Translate(array(
            'adapter'=>'ini',
            'local'=>$this->_defaultLanguage,
            'content'=>APPLICATION_PATH.DIRECTORY_SEPARATOR."modules".DIRECTORY_SEPARATOR.$this->_module
                .DIRECTORY_SEPARATOR.'languages'.DIRECTORY_SEPARATOR.$this->_defaultLanguage
                .DIRECTORY_SEPARATOR.$this->_name.".".$this->_defaultLanguage.".ini",
        ));
        try {
            if($this->_getParam('locale')!= NULL) {
                $this->_translate->setLocal($this->_getParam('locale'));
            }
            $addParam = $this->_getParam("addparams");
            if($addParam!= NULL) {
                $this->_addParams = json_decode(stripslashes($addParam));
                // basic decoding didn't work so try the more complicated method
                if($this->_addParams==NULL && strlen($addParam)>0) {
                    $this->_addParams = json_decode(base64_decode(html_entity_decode($addParam)));
                }
            }
            $this->view->translate = $this->_translate;
        } catch (exception $ex) {
            die("Error:".$ex->getMessage());
        }
        // get the current template
        $this->view->template = $this->_application->getTemplate();
        // display any messages
        if($this->_getParam('message')!= NULL && !isset($_SESSION['message'])) {
            $_SESSION['message']= true;
            $this->_application->enqueueMessage($this->_getParam('message'));
        }
        // display any notices
        if($this->_getParam('notice')!= NULL) {
            JError::raiseNotice(100,$this->_getParam('notice'));
        }
        // display any warnings
        if($this->_getParam('warning')!= NULL) {
            JError::raiseWarning(100,$this->_getParam('warning'));
        }
        // display any warnings
        if($this->_getParam('error')!= NULL) {
            JError::raiseError(4711,$this->_getParam('error'));
        }
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
                JToolBarHelper::title($this->_title);
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
}

