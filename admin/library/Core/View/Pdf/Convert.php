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
 * Description of convert
 *
 * tools to help convert a pdf document to other formats
 *
 * @author rbsolutions (rbsoultions.us@gmail.com)
 * @copyright (c) 2010 Red Black Tree LLC
 * @category ComZend
 * @package Core
 * @subpackage View_Pdf
 */
class Core_View_Pdf_Convert {
    /**
     * @var string path to the pdf file
     */
    protected $_pdfPath="";
    /**
     *
     * @var NULL|ImageMagic file
     */
    protected $_imFile = NULL;
    /**
     * __construct
     *
     * initializes the pdf converter
     *
     * @param string $pdfPath path to the pdf file
     * @param int $page the page to render
     */
    public function  __construct($pdfPath, $page=0)
    {
        // wrap in a try catch because errors need to display as images
        try {
            // check to ensure the file exists
            if(!file_exists($pdfPath)) {
                throw new Exception("Error, pdf file does not exist:".$pdfPath);
            }
            // save the path
            $this->_pdfPath=$pdfPath;
            // create the image
            $this->_imFile = new imagick($pdfPath[0]);

        } catch (Exception $ex) {
            die($ex->getMessage());
        }
        parent::construct();
    }
    /**
     * renderJpeg
     *
     * converts the image to jpeg and sends it to the browser
     */
    public function renderJpeg()
    {
        header('Content-Type: image/jpeg');
        $this->_imFile->setImageFormat('jpg');
        echo $this->_imFile;
        die();
    }
}
