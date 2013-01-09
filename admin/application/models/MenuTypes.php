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
defined ('_VALID_MOS') or
    die('Direct Access to this location is not allowed');
/**
 * Model_MenuTypes
 *
 * manages the compenents from Joomla
 *
 * @author rbsolutions (rbsoultions.us@gmail.com)
 * @copyright (c) 2010 Red Black Tree LLC
 * @category ComZend
 * @package Model
 */
class Model_MenuTypes extends Zend_Db_Table_Abstract
{
    /**
     * @var string Components name of the table within the database
     */
    protected $_name = 'menu_types';
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
     * getMenuType
     * 
     * gets a record from the database
     * 
     * @param int $id the unique id for the databse
     * @return Zend_db_Table_Row|NULL
     */
    public function getMenuType($id)
    {
        $mdl = $this;
        $resultSet = $mdl->find($id);
        if($resultSet->count() >0) {
            return $resultSet->current();
        }
        return NULL;
    }
    /**
     * addMenuType
     * 
     * creates a new menuType
     * 
     * @param type $name
     * @param type $title
     * @param type $description 
     */
    public function addMenuType($name,$title,$description)
    {
        $row = $this->createRow();
        $row->menutype = $name;
        $row->title=$title;
        $row->description = $description;
        return $row->save();
    }
    /**
     * getTypeByName
     * 
     * gets a menu type by the "menytype" field
     * 
     * @param string $name
     * @return Zend_db_Table_RowSet|NULL 
     */
    public function getTypeByName($name)
    {
        $select = $this->select()
                ->where('menutype=?',$name);
        $resultSet = $this->fetchAll($select);
        if($resultSet->count() >0) {
            return $resultSet->current();
        }
        return NULL;
    }
}

