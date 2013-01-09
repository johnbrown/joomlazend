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
 * @package Core
 * @link http://joomlazend.rbsolutions.us
 * @version $Id:$
 */
defined('_JEXEC') or 
    die('Direct Access to this location is not allowed');
/**
 * Description of Keywords
 *
 * Plugin that will automatically generate keywords
 *
 * @author rbsolutions (rbsoultions.us@gmail.com)
 * @copyright (c) 2010 Red Black Tree LLC
 * @category ComZend
 * @package Core
 * @subpackage View_Plugin
 */
jimport('joomla.html.html.behavior');
include_once 'behavior.php';
class Core_View_Plugin_Meta_Keywords extends Zend_Controller_Plugin_Abstract {
    /**
     * @var string the content to analyze
     */
    protected $_content="";
    /**
     * @var int minimum word lenght
     */
    protected $_minLength=5;
    /**
     * @var int weight added to heading itmes 
     */
    protected $_headingWeight=20;
    /**
     * @var int wieght added to link items
     */
    protected $_linksWeight=2;
    /**
     * @var int number of keywords to generate
     */
    protected $_numberOfKeywords=10;
    /**
     * @var array storage array for keywords
     */
    protected $_keywordArray = array();
    /**
     * @var array storage for links
     */
    protected $_links = array();
    /**
     * @var array storage for headings 
     */
    protected $_headings = array();
    /**
     * postDispatch
     * 
     * after the action has been dispached we will extract the keywords and 
     * automatically add them.
     * 
     * @param Zend_Controller_Request_Abstract $request 
     */
   public function postDispatch(Zend_Controller_Request_Abstract $request) {
       $application =& JFactory::getApplication();
       if(!$application->isAdmin()) {
            $response = $this->getResponse();
            $keywords = $this->extractKeywords($response->getBody());
            $document = &JFactory::getDocument();
            $document->setMetaData('keywords',$keywords);
       } else {
           echo JHtmlBehavior::keepalive();
       }
       parent::postDispatch($request);
   }
    /**
     * extractKeywords
     * 
     * Extract the keywords from the content string and return the keywords string
     * 
     * @return bool
     */
    public function extractKeywords($content="",$minLength=5,$headingWeight=20,
            $linksWeight=2,$numberOfKeywords=10)
    {
        $this->_content = $content;
        $this->_minLength = $minLength;
        $this->_headingWeight=$headingWeight;
        $this->_linksWeight = $linksWeight;
        $this->_numberOfKeywords=$numberOfKeywords;
        if($content!= NULL) {
            $this->_content = $content;
        }
        // search through the links
        $this->calculateLinks();
        // search through the heading
        $this->calculateHeading();
        // calculate based on the text
        $this->calculateText();
        
        // Sort the keywords
        arsort($this->_keywordArray);
        // Take only the number of keywords set in the config
        $this->_keywordArray = array_slice($this->_keywordArray,0,$this->_numberOfKeywords);
        $results= strtolower(implode(',',array_keys($this->_keywordArray)));
        if(strlen($results)>0) {
            return $results;
        } else {
            return "zfJoomla";
        } 
    }
    /**
     * calculateLinks
     * 
     * search for links and adds them to the keywords array with the appropriate
     * weight
     */
    public function calculateLinks() {
        // search through the links
        preg_match_all('#<a.*?>(.*?)</a.*?>#s',$this->_content,$this->_links);
        foreach($this->_links[1] as $key =>$value){
            $keywords = explode(' ',strip_tags($value));
            foreach($keywords as $id => $keyword){
                // Get the alpha numeric value for the keyword
                $keyword = preg_replace('/[^[:alpha:]]/', '', $keyword);
                if(strlen($keyword) >= $this->_minLength){
                    if(!array_key_exists($keyword,$this->_keywordArray)){
                        $this->_keywordArray[$keyword] = $this->_linksWeight;
                    }
                    else{
                        $this->_keywordArray[$keyword] += $this->_linksWeight;
                    }
                }
            }
        }
    }
    /**
     * calculateHeading
     * 
     * searches for heading items and adds them to the keywords  with their 
     * appropriate weight
     */
    public function calculateHeading() {
        //Count the heading keywords
        preg_match_all('#<h(.*?)>(.*?)</h.*?>#s',$this->_content,$this->_headings);
        foreach($this->_headings[2] as $key =>$value){
            $keywords = explode(' ',strip_tags($value));
            foreach($keywords as $id => $keyword){
                // Get the alpha numeric value for the keyword
                $keyword = preg_replace('/[^[:alpha:]]/', '', $keyword);
                if(strlen($keyword) >= $this->_minLength){
                    $divider = (int)$this->_headings[1][$key];
                    if($headingNumber == 0)$headingNumber = 1;
                    if(!array_key_exists($keyword,$this->_keywordArray)){
                        $this->_keywordArray[$keyword] = $this->_headingWeight/$headingNumber;
                    }
                    else{
                        $this->_keywordArray[$keyword] += $this->_headingWeight/$headingNumber;
                    }
                }
            }
        }
    }
    /**
     * calculateText
     * 
     * searchse through the remaining text and adds keywords to the keyword array
     * with the appropriate weight
     */
    public function calculateText() {
        // Count the text keywords including the heading and link texts!
        // Meaning these are counted double once with a rating of 1 and once with the rating set for them!
        $text = str_ireplace(array('/',"\n",'<br />','<br/>'),' ',$this->_content);
        $text = strip_tags($text);
        $keywords = explode(' ',$text);
        foreach($keywords as $key => $keyword){
            // Get the alpha numeric value for the keyword
            $keyword = preg_replace('/[^[:alpha:]]/', '', $keyword);
            if(strlen($keyword) >= $this->_minLength){
                if(!array_key_exists($keyword,$this->_keywordArray)){
                    $this->_keywordArray[$keyword] = 1;
                }
                else{
                    $this->_keywordArray[$keyword] += 1;
                }
            }
        }
    }
}
