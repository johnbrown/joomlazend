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
 * Install Module
 *
 * Controlls the install and management of Zend Framework Based Modules
 *
 *
 * @author rbsolutions (rbsoultions.us@gmail.com)
 * @copyright (c) 2010 Red Black Tree LLC
 * @category ComZend
 * @package Admin
 * @subpackage Form
 */
class Admin_Form_InstallModule extends Zend_Form
{
    /**
     * @var jquery object
     */
    protected $_jQuery = NULL;
    /**
     * @var array decrotors for the form
     */
    protected $_formDecorators = array(
        'FormElements',
        array(array('row'=>'HtmlTag'),array('tag'=>'tr')),
        array('HtmlTag',array('tag'=>'table')),
        'Form',
        array(array('div'=>'HtmlTag'),array('tag'=>'div','class'=>'ui-widget-content ui-corner-all')),
    );
    /**
     * @var array decorators for a generic element
     */
    protected $_elementDecorators = array(
        'ViewHelper',
        array('Errors',array('class'=>"errors ui-state-error ui-corner-all")),
        array(array('td'=>'HtmlTag'),array('tag'=>'td')),
        array('Label',array('tag'=>'td')),
    );
    /**
     * @var array decorators for a generic element
     */
    protected $_fileDecorators = array(
        'File',
        array('Errors',array('class'=>"errors ui-state-error ui-corner-all")),
        array(array('td'=>'HtmlTag'),array('tag'=>'td')),
        array('Label',array('tag'=>'td')),
    );
    /**
     * @var array decorators for a button element
     */
    protected $_buttonDecorators = array(
        'ViewHelper',
        array('Errors',array('class'=>"errors ui-state-error ui-corner-all")),
        array(array('data'=>'HtmlTag'),array('tag'=>'td','class'=>'element')),
    );
    /**
     * createJavaScript
     *
     * creates all of the javascript associated with this form
     */
    public function createJavaScript()
    {
        if ($this->_jQuery == NULL) {
            $this->_jQuery = new ZendX_JQuery_View_Helper_JQuery();
            $this->_jQuery->jQuery()->enable();
            $view = $this->getView();
        }
        ob_start();
        /**
         * @todo Add any jquery options here
         */
        $js = Core_View_Compress::compressJS(ob_get_clean());
        $this->_jQuery->jQuery()->addOnLoad($js);
    }
    /**
     * init
     *
     * Initializes the form
     **/
    public function init()
    {
        // set the default method
        $this->setMethod('post');
        $this->setName('InstallModule');
        $this->setAction($this->getView()->url(array(
            'controller'=>'modules',
            'action'=>'install',
        ),'administrator'));
        $this->setDecorators($this->_formDecorators);
        $this->setAttrib('enctype', 'multipart/form-data');
        // Add any additional Form elements here
        $module = new Zend_Form_Element_File('module', array(
            'label'=>$this->getView()->translate->_('Install Module:'),
            'destination'=>ROOT_DIR.DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR
                ."..".DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."tmp",
            'validators'=>array(
                    'Count'=>'1',
                    'size'=>'102400',
                    'extension'=>'zip',
               ),
            'required'=>true,
            'decorators'=>$this->_fileDecorators,
        ));
        $this->addElement($module);

        // Create the submit button
        $Submit =  $this->createElement('submit', 'Submit');
        $Submit->setLabel($this->getView()->translate->_('Install'))
                ->setAttrib('class', 'ui-button ui-state-default ui-corner-all')
                ->setDecorators($this->_buttonDecorators);
        $this->addElement($Submit);
    }
}

