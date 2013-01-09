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
 * Model_Menu
 *
 * manages the compenents from Joomla
 *
 * @author rbsolutions (rbsoultions.us@gmail.com)
 * @copyright (c) 2010 Red Black Tree LLC
 * @category ComZend
 * @package Model
 */
class Model_Menu extends Zend_Db_Table_Abstract
{
    /**
     * @var string Components name of the table within the database
     */
    protected $_name = 'menu';
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
     * getName 
     * 
     * gets the name of the database table
     * 
     * @return string table name
     */
    public static function getName() {
        $mdl = new self();
        return $mdl->_name;
    }
    /**
     * getMenu
     * 
     * gets a record from the database
     * 
     * @param int $id the unique id for the databse
     * @return Zend_db_Table_Row|NULL
     */
    public function getMenu($id)
    {
        $mdl = $this;
        $resultSet = $mdl->find($id);
        if($resultSet->count() >0) {
            return $resultSet->current();
        }
        return NULL;
    }
    /**
     * getByLink
     * 
     * gets a menu item by it's Link 
     * 
     * @param type $link
     * @return type 
     */
    public function getByLink($link="")
    {
        $select = $this->select()
                ->where('link=?',$link)
                ->where('published=1');
        $resultSet = $this->fetchAll($select);
        if($resultSet->count() >0) {
            return $resultSet->current();
        }
        return NULL;
    }
    /**
     * addMenuItem
     * 
     * creates a new menu item
     * 
     * @param type $menutype
     * @param type $title
     * @param type $alias
     * @param type $note
     * @param type $path
     * @param type $link
     * @param type $type
     * @param type $published
     * @param type $parent_id
     * @param type $level
     * @param type $component_id
     * @param type $ordering
     * @param type $checked_out
     * @param type $checked_out_time
     * @param type $browerNav
     * @param type $access
     * @param type $img
     * @param type $template_style_id
     * @param type $params
     * @param type $lft
     * @param type $rgt
     * @param type $home
     * @param string $langauge 
     */
    public function addMenuItem($menutype='',$title='',$alias='',$note='',$path='',$link='',$type='component',
            $published=1,$parent_id=1,$level=1,$component=NULL,$ordering=0,$checked_out=0,
            $checked_out_time=NULL,$browerNav=0,$access=0,$img='',$template_style_id=0,$params='',
            $lft=NULL,$rgt=NULL,$home=0,$langauge='*',$client_id=0)
    {
        $row = $this->createRow();
        if($lft==NULL|$rgt==NULL) {
            $max = $this->getMaxNeighbor();
            $lft = $max;
            $rgt=$lft+1;
            $root = $this->getMenu(1);
            $root->rgt = $rgt+1;
            $root->save();
        }
        $row->menutype=$menutype;
        $row->title = $title;
        $row->alias = $alias;
        $row->note=$note;
        $row->path = $path;
        $row->link = $link;
        $row->type = $type;
        $row->published = $published;
        $row->parent_id=$parent_id;
        $row->level = $level;
        if(is_int($component)) {
            $row->component_id=$component; 
        } else {
            $mdlComponent = new Model_Components();
            $componentRow = $mdlComponent->getComponentByName($component);
            if($componentRow!=NULL) {
                $row->component_id = $componentRow->extension_id;
            }
        }
        $row->ordering=$ordering;
        $row->checked_out = $checked_out;
        if($checked_out_time!= NULL) {
            $row->checked_out_time = $checked_out_time;
        }
        $row->browserNav=$browerNav;
        $row->access=$access;
        $row->img = $img;
        $row->template_style_id=$template_style_id;
        $row->params = $params;
        $row->lft = $lft;
        $row->rgt = $rgt;
        $row->home = $home;
        $row->language=$langauge;
        $row->client_id=$client_id;
        return $row->save();
    }
    /**
     * getMaxNeighbor
     * 
     * gets the max rgt
     * 
     * @return type 
     */
    public function getMaxNeighbor()
    {
        $select=$this->select()
                ->limit(5)
                ->order('rgt desc');
        $resultSet = $this->fetchAll($select);
        if($resultSet->count() >0) {
            return $resultSet->current()->rgt;
        }
        return 0;
    }
    /**
     * getMenuId
     * static function
     * 
     * gets a menu id for the current item
     * 
     * @return int|NULL 
     */
    public static function getMenuId()
    {
        return JSite::getMenu()->getActive()->id;
    }
}

