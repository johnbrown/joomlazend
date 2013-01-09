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
 * @package Model
 * @link http://joomlazend.rbsolutions.us
 * @version $Id:$
 */
defined ('_VALID_MOS') or
    die('Direct Access to this location is not allowed');
/**
 * Modules Model
 *
 * manages communication to the model for manageing zend based modules
 *
 * @author rbsolutions (rbsoultions.us@gmail.com)
 * @copyright (c) 2010 Red Black Tree LLC
 * @category ComZend
 * @package Model
 */
class Model_ZFModules extends Zend_Db_Table_Abstract
{
    /**
     * @var string Acceptance name of the table within the database
     */
    protected $_name = 'zf_modules';
    /**
     * @var string name of the database to connect to
     */
    protected $_use_adapter = "joomla";
     /**
     * __construct
     *
     * queries the zend registry to select the proper database
     *
     * @param mixed $config
     * @return mixed
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
    public function getModule($id)
    {
        $mdl = $this;
        $resultSet = $mdl->find($id);
        if($resultSet->count() >0) {
            return $resultSet->current();
        }
        return NULL;
    }
    /**
     * createTable
     *
     * if the current table does not exists, create it
     */
    public function createTable()
    {
        try {
            $desc = $this->_db->describeTable($this->_name);
        } catch (exception $ex) {
            // table does not exist, create it
            $createSql = "CREATE TABLE `".$this->_name
                   ."` (id INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT, "
                   ."module VARCHAR(32) NOT NULL, "
                   ."default_controller VARCHAR(32) DEFAULT 'index', "
                   ."default_action VARCHAR(32) DEFAULT 'index', "
                   ."created TIMESTAMP NOT NULL DEFAULT NOW(), "
                   ."enabled TINYINT NOT NULL DEFAULT 1, "
                   ."published TINYINT NOT NULL DEFAULT 0, "
                   ."params TEXT, "
                   ."description TEXT NOT NULL DEFAULT '')";
            $this->_db->query($createSql);
            $indexSQL = "CREATE INDEX `id` on `".$this->_name."` (`id`)";
            $this->_db->query($indexSQL);
        }
    }
    /**
     * removeTable
     *
     * removes the table from the database
     */
    public function removeTable()
    {
        $dropSql = "DROP TABLE IF EXISTS `".$this->_name."`";
        $this->_db->query($dropSql);
    }
    /**
     * addModule
     *
     * adds a new module to the database
     *
     * @param string $module max_32
     * @param string $dController max_32
     * @param string $dAction max_32
     * @param array $params
     * @param string $description
     * @param boolean $published
     * @return int
     */
    public function addMoudule($module,$dController, $dAction,array $params=array(),
            $description="", $published=0)
    {
        $row = $this->createRow();
        $row->module = $module;
        $row->default_controller = $dController;
        $row->default_action = $dAction;
        $row->enabled = 1;
        $row->published = $published;
        $row->params = json_encode($params);
        $row->description = $description;
        return $row->save();
    }
    /**
     * removedModule
     * 
     * removes the module named
     * 
     * @param string $module
     * @return mixed
     */
    public function removeModule($module)
    {
        $row = $this->getModuleByName($module);
        return $row->delete();
    }
    /**
     * getModules
     *
     * gets the modules from the database
     *
     * @param boolean|NULL $published
     * @param boolean|NULL $enabled
     * @return Zend_Db_Table_RowSet|NULL
     */
    public function getModules($published=NULL,$enabled=NULL)
    {
        $select = $this->select();
        // check for filtering conditions
        if($published!=NULL) {
            $select->where('published=?',$published);
        }
        if($enabled!=NULL) {
            $select->where('enabled=?',$enabled);
        }
        $resultSet = $this->fetchAll($select);
        if($resultSet->count() >0) {
            return $resultSet;
        }
        return NULL;
    }
    /**
     * getModuleByName
     *
     * gets a module from the database by name
     *
     * @param string $name
     * @return Zend_Db_Table_Row|NULL
     */
    public function getModuleByName($name)
    {
        $select = $this->select();
        $select->where('module=?',$name);
        $resultSet = $this->fetchAll($select);
        if($resultSet->count() >0) {
            return $resultSet->current();
        }
        return NULL;
    }
}

