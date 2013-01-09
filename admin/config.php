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
 * @link http://zfjoomla.rbsolutions.us
 * @version $Id:$
 */
define ('_VALID_MOS',true);
defined("ZEND_COMPONENT_NAME")
        || define('ZEND_COMPONENT_NAME','com_zend');
defined('ROOT_DIR')
        ||define('ROOT_DIR', dirname(__FILE__));
// Define pat to application directory
defined('APPLICATION_PATH')
	|| define('APPLICATION_PATH', ROOT_DIR . '/application');
defined('APPLICATION_URL')
	|| define('APPLICATION_URL', '/administrator/components/'
                . ZEND_COMPONENT_NAME . '/application');
// Define application environment
defined('APPLICATION_ENV')
	|| define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ?
	getenv('APPLICATION_ENV'):'development'));
