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

/**
 * INstall
 *
 * Zend Framework install file that manages the install process
 *
 *
 * @author rbsolutions (rbsoultions.us@gmail.com)
 * @copyright (c) 2010 Red Black Tree LLC
 * @category ComZend
 * @package Install
 */
class install {
    public function  __construct()
    {
        require_once 'installZend.php';
        // start the install
        $install = new installZend();
    }
}
$inst = new install();
