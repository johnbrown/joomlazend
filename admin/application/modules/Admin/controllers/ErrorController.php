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
defined('_JEXEC') or 
    die('Direct Access to this location is not allowed');
/**
 * ErrorController
 *
 * Error controller
 *
 *
 * @author rbsolutions (rbsoultions.us@gmail.com)
 * @copyright (c) 2010 Red Black Tree LLC
 * @category ComZend
 * @package Admin
 * @subpackage Controller
 */
class Admin_ErrorController extends Zend_Controller_Action{
    /**
     * erroAction
     *
     * displays an error
     */
    public function errorAction() {
        $errors = $this->_getParam('error_handler');
        switch($errors->type) {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
                // 404 error -- controller or action not found
                $this->getResponse()->setHttpResponseCode(404);
                $this->view->message = 'Page not found';
                var_dump($this->getRequest());
                break;
            default:
                // application error
                $this->getResponse()->setHttpResponseCode(500);
                $this->view->message = $errors->exception->getMessage();
                break;
        }
        $this->view->exception = $errors->exception;
        $this->view->request = $errors->request;
    }
    /**
     * noauthAction
     * shows the permission deneid form
     */
    public function noauthAction () {
        
    }
};