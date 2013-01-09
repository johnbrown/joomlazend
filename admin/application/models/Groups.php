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
 * @package Model
 * @link http://joomlazend.rbsolutions.us
 * @version $Id:$
 */
defined('_JEXEC') or 
    die('Direct Access to this location is not allowed');
/**
 * Model_Groups
 *
 * manages the Groups from Joomla
 *
 * @author rbsolutions (rbsoultions.us@gmail.com)
 * @copyright (c) 2010 Red Black Tree LLC
 * @category ComZend
 * @package Model
 */
class Model_Groups extends Zend_Db_Table_Abstract {
    /**
     * @var string Acceptance name of the table within the database
     */
    protected $_name = 'groups';
    /**
     * @var string name of the database to connect to
     */
    protected $_use_adapter = "joomla";
     /**
     * __construct
     *
     * queries the zend registry to select the proper database
     *
     * @param <type> $config
     * @return <type>
     */
    public function  __construct($config = null)
    {
        $this->_name = Zend_Registry::get('dbprefix') . $this->_name;
        if (isset($this->_use_adapter)) {
            $dbAdapters = Zend_Registry::get('dbAdapters');
            $config = ($dbAdapters[$this->_use_adapter]);
        }
        return parent::__construct($config);
    }
    /**
     * get{$name}
     * 
     * gets a record from the database
     * 
     * @param int $id the unique id for the databse
     * @return Zend_db_Table_Row|NULL
     */
    public function getGroup($id)
    {
        $mdl = $this;
        $resultSet = $mdl->find($id);
        if($resultSet->cunt() >0) {
            return $resultSet->current();
        }
        return NULL;
    }
    /**
     * getGroups
     *
     * gets all of the groups
     *
     * @return Zend_db_Table_RowSet|NULL
     */
    public function getGroups()
    {
        $mdl = $this;
        $resultSet = $mdl->fetchAll();
        if($resultSet->count()>0) {
            return $resultSet;
        }
        return NULL;
    }
}

