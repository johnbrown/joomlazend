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
 * Model_Components
 *
 * manages the compenents from Joomla
 *
 * @author rbsolutions (rbsoultions.us@gmail.com)
 * @copyright (c) 2010 Red Black Tree LLC
 * @category ComZend
 * @package Model
 */
class Model_Components extends Zend_Db_Table_Abstract
{
    /**
     * @var string Components name of the table within the database
     */
    protected $_name = 'extensions';
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
     * getcomponent
     * 
     * gets a record from the database
     * 
     * @param int $id the unique id for the databse
     * @return Zend_db_Table_Row|NULL
     */
    public function getComponent($id)
    {
        $mdl = $this;
        $resultSet = $mdl->find($id);
        if($resultSet->count() >0) {
            return $resultSet->current();
        }
        return NULL;
    }
    /**
     * getComponentByName
     * 
     * gets a component by it's name
     * 
     * @param string $name
     * @return Zend_db_table_Row|NULL
     */
    public function getComponentByName($name)
    {
        return $this->getExtensionByName($name,'component');
    }
    /**
     * getModuleByName
     * 
     * gets a module by it's name
     * 
     * @param string $name
     * @return Zend_db_table_Row|NULL
     */
    public function getModuleByName($name)
    {
       return $this->getExtensionByName($name,'module'); 
    }
    /**
     * getPluginByName
     * 
     * gets a plugin by it's name
     * 
     * @param string $name
     * @return Zend_db_table_Row|NULL
     */
    public function getPluginByName($name)
    {
       return $this->getExtensionByName($name,'plugin'); 
    }
    /**
     * getExtensionByName
     * 
     * gets an extension by it's name
     * 
     * @param string $name
     * @param string|NULL $type
     * @return Zend_db_table_Row|NULL
     */
    public function getExtensionByName($name, $type=NULL)
    {
        $select = $this->select();
        $select->where('name=?',$name);
        if($type!=NULL) {
            $select->where('type=?',$type);
        }
        $resultSet = $this->fetchAll($select);
        if($resultSet->count() >0) {
            return $resultSet->current();
        }
        return NULL;
    }
    /**
     * addComponent
     *
     * creates a new component
     *
     * @param string $name
     * @param array|string $params
     * @return int the id for the newly created component
     */
    public function addComponent($name, $params)
    {
        $mdl = $this;
        $row = $mdl->createRow();
        $row->name = $name;
        $row->type='component';
        $row->element = $name;
        $row->params = $params;
        return $row->save();
    }
    /**
     * createComponent
     * 
     * @param string $name
     * @param string $params
     * @return int
     */
    public function createComponent($name, $params='')
    {
        $mdl = $this;
        $newComponent = $mdl->addComponent(
                $name,
                $params
                );
        return $newComponent;
    }
    /**
     * getParams
     * 
     * gets the params from the database
     * 
     * @param string $name
     * @return array 
     */
    public function getParams($name)
    {
        $mdl = $this;
        $component = $mdl->getComponentByName($name);
        $params = array();
        if($component!= NULL) {
            $p=json_decode($component->params);
            foreach ($p as $name=>$val) {
                $params[$name] = $val;
            }
        }
        return $params;
    }
    /**
     * setParams
     *
     * sets the parameters for a component
     *
     * @param string $name
     * @param array $params
     * @return bool
     */
    public function setParams($name,array $params = array())
    {
        $mdl = $this;
        $component = $mdl->getComponentByName($name);
        $component->params = json_encode($params);
        return $component->save();
    }
}

