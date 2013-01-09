<?php
/**
 * ZFJoomla
 * Zend Framework for Joomla
 * Red Black Tree LLC
 *
 * default file for com_zend
 *
 * @author rbsolutions (rbsoultions.us@gmail.com)
 * @copyright (c) 2010 Red Black Tree LLC
 * @category ComZend
 * @package Core
 * @link http://joomlazend.rbsolutions.us
 * @version $Id:$
 */
require_once(getcwd().DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR
        .'com_zend'.DIRECTORY_SEPARATOR.'config.php');

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
try {
    $application->run();
} catch(Exception $ex) {
    echo "<h1>Error:".$ex->getMessage()."</h1>";
    echo "<div>".$ex->__toString()."</div>";
    die('end');
}
