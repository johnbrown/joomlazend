<?php
/**
 * JoomlaZend
 * Zend Framework for Joomla
 * Red Black Tree LLC
 *
 *
 * @author rbsolutions (rbsoultions.us@gmail.com)
 * @copyright (c) 2010 Red Black Tree LLC
 * @category JoomlZend
 * @package Administrator_ComZend_Application
 * @link http://joomlazend.rbsolutions.us
 * @version $Id:$
 */

/**
 * Bootstrap
 *
 * Zend Framework bootstrap that loads config variables from the Joomla Config
 * file, creates the database connections and registers autoloading directories
 * and creates the view
 *
 *
 * @author rbsolutions (rbsoultions.us@gmail.com)
 * @copyright (c) 2010 Red Black Tree LLC
 * @category JoomlZend
 * @package Administrator_ComZend_Application_Bootstrap
 */
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap {
    /**
     * _initConfig
     *
     * loads a customized default ini file allowing variables
     *
     * @throws OSA_Exception
     */
    protected function _initConfig()
    {
        try {
            Zend_Registry::set('componentName',ZEND_COMPONENT_NAME);
        } catch (Zend_Exception $ex) {
            throw new Exception("Error in _initConfig:" . $ex->getMessage());
        }
    }
    /**
     * _initDatabase
     *
     * Initializes the database into an array within the registry
     *
     * @return mixed
     * @throws OSA_Exception
     */
    protected function _initDatabase()
    {
        // use the regitsry to store the db
        if(Zend_Registry::isRegistered('dbAdapters')) {
            return;
        }
        
        // otherwise create your own
        try {
            //$db = & JFactory::getDBO();
            $config =& JFactory::getConfig();
            $dbAdapters['joomla'] = new Zend_Db_Adapter_Mysqli(array(
                'host'=>$config->getValue('host'),
                'dbname'=>$config->getValue('db'),
                'password'=>$config->getValue('password'),
                'username'=>$config->getValue('user'),
            ));
            Zend_Registry::set('dbprefix',$config->getValue('dbprefix'));
            Zend_Registry::set('dbAdapters',$dbAdapters);
        } catch (exception $ex) {
            echo "Error in _initDatabase:".$ex->__toString();
        }
    }
    /**
     * initAutoload()
     *
     * Initialize the autoloader and return to bootstrap
     *
     * @return mixed
     */
    protected function _initAutoload()
    {     
        $resourceLoader = new Zend_Loader_Autoloader_Resource(array(
           'basePath'=>APPLICATION_PATH,
            'namespace'=>'',
            'resourceTypes'=>array(
              'form'=>array(
                  'path'=>'forms',
                  'namespace'=>'Form_',
              ),
              'model'=>array(
                  'path'=>'models',
                  'namespace'=>'Model_'
              ),
              'ZendX'=>array(
                  'path'=>'..'.DIRECTORY_SEPARATOR.'library'.DIRECTORY_SEPARATOR.'ZendX',
                  'namespace'=>'ZendX_',
              ),
              'Core'=>array(
                  'path'=>'..'.DIRECTORY_SEPARATOR.'library'.DIRECTORY_SEPARATOR.'Core',
                  'namespace'=>'Core_',
              ),
            ),
        ));
        
        // check for a registry defined autoloader
        if(Zend_Registry::isRegistered('autoloader')) {
            return Zend_Registry::get('autoloader');
        }
        
        
         // Add autoloader empty namespace
        $autoLoader = Zend_Loader_Autoloader::getInstance();
        Zend_Registry::set('autoloader', $autoloader);
        // Return it so that it can be stored by the bootstrap
        return $autoLoader;
    }
    /**
     * _initView
     *
     * intisializes a view and adds jquery
     * Note use of jquery in joomla requires no conflict mode
     *
     * @return Zend_View
     */
    protected function _initView()
    {
        // get the joomla document
        $document =& JFactory::getDocument();
        if (Zend_Registry::isRegistered('view')) {
            $view = Zend_Registry::get('view');
        } else {
            $view = new Zend_View();
            $view->addHelperPath(APPLICATION_PATH.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR
                    .'library'.DIRECTORY_SEPARATOR.'ZendX'.DIRECTORY_SEPARATOR
                    .'JQuery'.DIRECTORY_SEPARATOR.'View'.DIRECTORY_SEPARATOR
                    .'Helper', 'ZendX_JQuery_View_Helper');
            $view->addHelperPath(APPLICATION_PATH.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR
                    .'library'.DIRECTORY_SEPARATOR.'Core'.DIRECTORY_SEPARATOR
                    .'View'.DIRECTORY_SEPARATOR.'Helper', 'Core_View_Helper');
            ZendX_JQuery_View_Helper_JQuery::enableNoConflictMode();
            $view->jQuery()->enable();
            $view->jQuery()->uiEnable();
            $view->jQuery()->setUiVersion('1.8');
            $view->jQuery()->setVersion('1.4.2');
            $view->headLink()->appendStylesheet(APPLICATION_URL.'/public/css/jquery-ui-1.8.6.custom.css');
            Zend_Registry::set('view',$view);
        }
        $viewRenderer = new Zend_Controller_Action_Helper_ViewRenderer();
        $viewRenderer->setView($view);
        Zend_Controller_Action_HelperBroker::addHelper($viewRenderer);
        return $view;
    }
     /**
     * _initRoutes
     *
     * initialize the routes
     */
    protected function _initRoutes()
    {
        // get the front controller
        $front = Zend_Controller_Front::getInstance();
        $front->setBaseUrl('/');
            
        if(!Zend_Registry::isRegistered('router')) {
            // create the router
            $router = $front->getRouter();
            // recreate the administrator Route
            $administrator = new Zend_Controller_Router_Route_Regex(
                    'administrator/index.php?option='.ZEND_COMPONENT_NAME
                    .'\&view=zend\&zmodule\=(.+)\&zcontroller\=(.+)\&zaction\=(.+)\&addparams\=(.+)',
                    array(
                        'module'=>'Admin',
                        'controller'=>'index',
                        'action'=>'index',
                        'addparams'=>'',
                    ),
                    array(
                        1=>'module',
                        2=>'controller',
                        3=>'action',
                        4=>'addparams',
                    ),
                    'administrator/index.php?option='.ZEND_COMPONENT_NAME.'&view=zend&zmodule=%s&zcontroller=%s&zaction=%s&addparams=%s'
                );
            $router->addRoute('administrator',$administrator);
            // recreate the default Route
            $default = new Zend_Controller_Router_Route_Regex(
                    'index.php?option='.ZEND_COMPONENT_NAME
                    .'\&view=zend\&zmodule\=(.+)\&zcontroller\=(.+)\&zaction\=(.+)\&addparams\=(.+)',
                    array(
                        'module'=>'Admin',
                        'controller'=>'index',
                        'action'=>'index',
                        'addparams'=>'',
                    ),
                    array(
                        1=>'module',
                        2=>'controller',
                        3=>'action',
                        4=>'addparams',
                    ),
                    'index.php?option='.ZEND_COMPONENT_NAME.'&view=zend&zmodule=%s&zcontroller=%s&zaction=%s&addparams=%s'
                );
            $router->addRoute('default',$default);

            Zend_Registry::set('router',$router);
        } else {
            $router = Zend_Registry::get('router');
        }
        
        // register plugins
        $front->registerPlugin(new Core_View_Plugin_Meta_Keywords());
        
        
        // give the front controller back our modified router
        $front->setRouter($router);
    }
    
    /**
     * _initSession
     * 
     * resets the session timer if it exists
     */
    public function _initSession()
    {
        try {
            Model_Session::sUpdateSession();
        } catch (Exception $ex) {
            die($ex->getMessage());
        }
    }
    
    /** 
     * create the cache
     */
    public function _initCache()
    {
        if (Zend_Registry::isRegistered('cache')) {
            return;
        } 
        $frontendOptions = array(
           'lifetime' => 7200,
           'automatic_serialization' => true
        );

        $backendOptions = array(
        'cache_dir' => realpath(ROOT_DIR . DIRECTORY_SEPARATOR . '..' 
            . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' 
            . DIRECTORY_SEPARATOR . 'cache'),
        );

        $dataCache = Zend_Cache::factory('Core',
                                     'File',
                                     $frontendOptions,
                                     $backendOptions);

        $manager = new Zend_Cache_Manager;
        $manager->setCache('data', $dataCache);

        Zend_Registry::set('cache',$manager);
    }
}
