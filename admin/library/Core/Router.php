<?php
/**
 * ZFJoomla
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
 * Core_Router
 *
 * Routes the action to the proper module, controller and action.  Also adds
 * the styles and javascript from the head controller and sends it to the
 * joomla controlls *
 *
 * @author rbsolutions (rbsoultions.us@gmail.com)
 * @copyright (c) 2010 Red Black Tree LLC
 * @category ComZend
 * @package Core
 * @subpackage Router
 */
class Core_Router extends Zend_Controller_Plugin_Abstract{
    /**
     * @var Joomla Application
     */
    protected $_application = NULL;
    /**
     * preDispatch
     *
     * routes the system to the appropriate actions
     *
     * @param Zend_Controller_Request_Abstract $request
     */
    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request)
    {

        // get the application
        $this->_application =& JFactory::getApplication();

        $module = $request->getParam('zmodule',NULL);
        $controller = $request->getParam('zcontroller','index');
        $action = $request->getParam('zaction','index');

        // restrict access to the admin form
        if(!$this->_application->isAdmin() && $module=='Admin') {
            $module = 'Default';
            $controller='error';
            $action = 'noauth';
        } else if($this->_application->isAdmin() && $module==NULL) {
            $module = "Admin";
        } else if($module==NULL){
            $module = "Default";
        }

        $request->setParam('module', $module);
        $request->setModuleName($module);
        // for some reason zend will not automatically recongnize the module's
        // controller directory so we need to add it
        $front = Zend_Controller_Front::getInstance();
        $front->addControllerDirectory(APPLICATION_PATH ."/modules/" . $module . "/controllers");


        $request->setParam('controller', $controller);
        $request->setControllerName($controller);


        $request->setActionName($action);
        $request->setParam('action', $action);
        parent::dispatchLoopStartup($request);
    }
    /**
     * postDispatch
     *
     * @todo add code to allow for conditional css, see
     * http://docs.joola.org/Adding_JavaScript_and_CSS_to_the_page
     */
    public function dispatchLoopShutdown()
    {
        // get the joomla document
        $document =& JFactory::getDocument();
 
        $view = Zend_Registry::get('view');
        $document->addScriptDeclaration("</script>".$view->JQuery()."<script>",'text/javascript');
        
        // step through the stylesheets
        foreach($view->headLink() as $link) {
            $document->addStyleSheet($link->href,$link->type,$link->media);
        }
        // step through the styles and addd them to the stack
        foreach($view->headStyle() as $style) {
            $document->addStyleDeclaration($style->content);
        }
        
        // step through the scripts and add them to the stack
        foreach($view->headScript() as $script) {
            if($script->source ==NULL) {
                // add the script file
                $document->addScript($script->attributes['src']);
            } else {
                // add the script
                $document->addScriptDeclaration($script->source,$script->type);
            }
        }
            
        parent::dispatchLoopShutdown();
    }
}
