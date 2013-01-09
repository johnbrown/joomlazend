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
defined('_JEXEC') or 
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
class Model_Modules extends Zend_Db_Table_Abstract
{
    /**
     * @var string Acceptance name of the table within the database
     */
    protected $_name = 'modules';
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
     * removeModuleByTitle
     * 
     * removes the module named
     * 
     * @param string $module
     * @return mixed
     */
    public function removeModuleByTitle($module)
    {
        $row = $this->getModuleByTitle($module);
        return $row->delete();
    }
    /**
     * getModules
     *
     * gets the modules from the database
     *
     * @param string|NULL $name the title of the module
     * @param boolean|NULL $published
     * @return Zend_Db_Table_RowSet|NULL
     */
    public function getModules($name=NULL, $published=NULL)
    {
        $select = $this->select();
        // check for filtering conditions
        if($published!=NULL) {
            $select->where('published=?',$published);
        }
        if($name!=NULL) {
            $select->where('title=?',$name);
        }
        $resultSet = $this->fetchAll($select);
        if($resultSet->count() >0) {
            return $resultSet;
        }
        return NULL;
    }
    /**
     * getModuleByTitle
     *
     * gets a module from the database by title
     *
     * @param string $name
     * @return Zend_Db_Table_Row|NULL
     */
    public function getModuleByTitle($name)
    {
        $select = $this->select();
        $select->where('title=?',$name);
        $resultSet = $this->fetchAll($select);
        if($resultSet->count() >0) {
            return $resultSet->current();
        }
        return NULL;
    }
    /**
     * addModule 
     * 
     * creates a new module
     * 
     * @param type $title
     * @param type $note
     * @param type $content
     * @param type $position
     * @param type $module
     * @param type $access
     * @param type $showtitle
     * @param array $params
     * @param type $clientId
     * @param type $language
     * @return type 
     */
    public function addModule($title,$note,$content,$position,$module, 
            $access=1,$showtitle=1,array $params=array(), $clientId=0, $language='*')
    {
        $row = $this->createRow();
        $row->title = $title;        
        $id = $row->save();
        $values = array(
            'title'=>$title,
            'note'=>$note,
            'content'=>$content,
            'ordering'=>1,
            'position'=>$position,
            'checked_out'=>0,
            'published'=>0,
            'module'=>$module,
            'access'=>$access,
            'showtitle'=>$showtitle,
            'params'=>$params,
            'client_id'=>$clientId,
            'language'=>$language,
        );
        $this->updateModule($id, $values);
        
        return $id;
    }
    /**
     * updateModule
     * 
     * updates a modules's values
     * 
     * @param type $id
     * @param array $values
     * @return type 
     */
    public function updateModule($id, array $values)
    {
        $row = $this->getModule($id);
        if($row==NULL) {
            throw new Exception('Error updating module, could not find:'.$id);
        }
        foreach($values as $name=>$value) {
            switch($name) {
                case 'title':
                case 'content':
                case 'ordering':
                case 'position':
                case 'checked_out':
                case 'checked_out_time':
                case 'publish_up':
                case 'publish_down':
                case 'published':
                case 'module':
                case 'access':
                case 'showtitle':
                case 'client_id':
                case 'language':
                    $row->$name=$value;
                    break;
                case 'params':
                    $row->$name = json_encode($value);
                    break;
                default:
                    break;
            }
        }
        return $row->save();
    }
}

