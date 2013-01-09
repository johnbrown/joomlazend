<?php 
/**
 * Model_Session 
 * 
 * 
 */
defined ('_VALID_MOS') or
    die('Direct Access to this location is not allowed');
/**
 * Description of Session
 *
 * @author brown298
 */
class Model_Session extends Zend_Db_Table_Abstract {
    
    /**
     * @var string Acceptance name of the table within the database
     */
    protected $_name = 'session';
    
    /**
     * @var string name of the database to connect to
     */
    protected $_use_adapter = "joomla";
    
     /**
     * __construct
     *
     * queries the zend registry to select the proper database
     *
     * @param array config
     * @return Model_Session
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
     * updateSession
     * 
     * updates the session to prevent timeouts
     * 
     * @param type $time
     */
    public function updateSession($time = NULL)
    {
        $query = $this->select()
                ->where('session_id = ?',$_SESSION['__default']['session.token']);
        $result = $this->fetchAll($query);
        if($result->count() >0) {
            foreach($result as $res) {
                // gather the data
                $prevTime = $res->time;
                if($time ==NULL) {
                    $currTime = time();
                } else {
                    $currTime=$time;
                }
                $data = $res->data;
                
                // replace the time values
                $data = str_replace('session.timer.last";i:'.strval($prevTime - 1), 
                        'session.timer.last";i:'.strval($currTime - 1), $data);
                $data = str_replace('session.timer.now";i:'.strval($prevTime), 
                        'session.timer.now";i:'.strval($currTime), $data);
                $data = str_replace('expiry";i:'.strval($prevTime - 1), 
                        'expiry";i:'.strval($currTime - 1), $data);

                // update the database
                $res->data= $data;
                $res->time = $currTime;
                $res->save();
            }
        }
    }
    
    /**
     * sUpdateSession
     * static call
     * 
     * updates the session to prevent timeouts
     * 
     * @param type $time
     * @return type 
     */
    public static function sUpdateSession($time = NULL)
    {
        $obj = new self();
        return $obj->updateSession();
    }
}
