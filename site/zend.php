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
$cache = JCache::getInstance();

define('ROOT_DIR',dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'
        .DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'
        .DIRECTORY_SEPARATOR.'com_zend');

if($cache->getCaching()) {
    // get the output
    $id = JCache::makeId();
    $data = $cache->get($id);
    if($data === false) {
        ob_start();
        require_once(ROOT_DIR.DIRECTORY_SEPARATOR.'admin.zend.php');
        $data = ob_get_clean();
        $cache->store($data,$id);
    } 
    echo $data;
} else {
    require_once(ROOT_DIR.DIRECTORY_SEPARATOR.'admin.zend.php');
}