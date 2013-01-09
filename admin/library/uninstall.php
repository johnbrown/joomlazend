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
require_once(getcwd().DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR
        .'com_zend'.DIRECTORY_SEPARATOR.'config.php');
/**
 * Uninstall
 *
 * Zend Framework uninstall file that manages the uninstall processs
 *
 *
 * @author rbsolutions (rbsoultions.us@gmail.com)
 * @copyright (c) 2010 Red Black Tree LLC
 * @category ComZend
 * @package Install
 */
class uninstall {
    /**
     * __construct
     *
     * default constructor handles the uninstall process
     */
    public function __construct()
    {
        // Ensure library/ is on include_path
        set_include_path(implode(PATH_SEPARATOR, array(
                '.',
                ROOT_DIR . "/library",
                ROOT_DIR . '/application/models',
                get_include_path(),
        )));


        require_once 'Zend/Application.php';

        //Create application, bootstarp, and run
        $application = new Zend_Application(
                APPLICATION_ENV,
                APPLICATION_PATH . '/configs/application.ini'
        );
        try {
            $application->bootstrap();
        } catch(Exception $ex) {
            die('Error loading bootstrap');
        }
        $front= Zend_Controller_Front::getInstance();
        $front->setRequest(new Zend_Controller_Request_Simple());
        $front->setResponse(new Zend_Controller_Response_Http());
        $view = new Zend_View();
        echo $view->action('uninstall','config','Admin');
    }
}
$uninstall = new uninstall();