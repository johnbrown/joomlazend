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
 * Admin_Bootstrap
 *
 * bootstraps the admin module
 *
 *
 * @author rbsolutions (rbsoultions.us@gmail.com)
 * @copyright (c) 2010 Red Black Tree LLC
 * @category ComZend
 * @package Admin
 */
class Admin_Bootstrap extends Zend_Application_Module_Bootstrap {
    /**
     * _initAutoload()
     *
     * Initialize the autoloader and return to bootstrap
     *
     * @return mixed
     */
    protected function _initAutoload() {
        // Add autoloader empty namespace
        $autoLoader = Zend_Loader_Autoloader::getInstance();
        $resourceLoader = new Zend_Loader_Autoloader_Resource(array(
           'basePath'=>APPLICATION_PATH."/modules/Admin",
            'namespace'=>'',
            'resourceTypes'=>array(
              'form'=>array(
                  'path'=>'forms/',
                  'namespace'=>'Form_',
              ),
            ),
        ));
        // Return it so that it can be stored by the bootstrap
        return $autoLoader;
    }
}
