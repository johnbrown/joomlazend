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
defined ('_VALID_MOS') or
    die('Direct Access to this location is not allowed');
/**
 * Description of compress
 *
 * tool to help compress output to save bandwidth
 *
 * @author rbsolutions (rbsoultions.us@gmail.com)
 * @copyright (c) 2010 Red Black Tree LLC
 * @category ComZend
 * @package Core
 * @subpackage View
 */
class Core_View_Image {
    /**
     * @var name of the object for the HTML output
     */
    public $name = 'image';
    /**
     * @var string the css class of the object
     */
    public $class="image";
    /**
     * @var string the alternate description for the image
     */
    public $alt = '';
    /**
     * @var string the output path for files
     */
    public $rootpath = "";
    /**
     * @var string url of the file
     */
    protected $_url;
    /**
     * @var string current location of the file
     */
    protected $_location;
    /**
     * @var int the mininmum zoom ratio as a percentage
     */
    protected $_minZoom = 1;
    /**
     * @var int the maximum zoom ratio as a percentage
     */
    protected $_maxZoom = 100;
    /**
     * @var int default maximum image width
     */
    protected $_max_x=300;
    /**
     * @var int default maximum image height
     */
    protected $_max_y=300;
    /**
     * @var int original width of the image
     */
    protected $width = 100;
    /**
     * @var int original height of the image
     */
    protected $height = 100;
    /**
     * @var int new height for the ouput image
     */
    protected $newHeight = 100;
    /**
     * @var int the new width for the output image
     */
    protected $newWidth = 100;
    /**
     * @var float ratio of the old image width to the new
     */
    protected $widthRatio = 100;
    /**
     * @var float ratio of the old image height to the new
     */
    protected $heightRatio = 100;
    /**
     * @var object information reguarding the image
     */
    protected $imageInfo;
    /**
     * @var object the new image
     */
    protected $image;
    /**
     * @var object the output image
     */
    protected $outimg;
    /**
     * @var int vertical position of the element
     */
    protected $posY = 0;
    /**
     * @var horizontal position of the element
     */
    protected $posX = 0;
    /**
     * @var the rgb values of the background
     */
    protected $picBG = "255,255,255";
    /**
     * @var the rgb values of the forground image
     */
    protected $picFG = "104,104,104";
    /**
     * @var int the font to use
     */
    protected $font = 1;
    /**
     * @var array array of attributes
     */
    protected $_attributes=array();
    /**
     * @var string path to save the image in
     */
    public $save_location = NULL;
    /**
     * @var string|NULL either width, height, or null
     */
    public $auto_dimension = NULL;
    /**
     *
     * @param <type> $name
     * @param <type> $value
     */
    public function setAttrib($name, $value) {
        switch($name) {
            case "alt":
                $this->alt = $value;
                break;
            case "class":
                $this->class = $value;
                break;
            case "name":
                $this->name = $value;
                break;
            case "width":
                $this->_attributes[$name] = $value;
                $this->setWidth($value);
                break;
            case "height":
                $this->_attributes[$name] = $value;
                $this->setHeight($value);
                break;
            default:
                $this->_attributes[$name] = $value;
                break;
        }
    }
    /**
     * setWidth
     *
     * Sets the new default width of the image
     *
     * @param int $width witdth to be set
     */
    public function setWidth($width) {
        $this->_max_x = $width;
    }
    /**
     * getWidth
     * @return int the width of the image
     */
    public function getWidth() {
        return $this->_max_x;
    }
    /**
     * setHeight
     *
     * Sets the new default height of the image
     *
     * @param <type> $height height to be set
     */
    public function setHeight($height) {
        $this->_max_y =$height;
    }
    /**
     * getHeight
     * @return int the height of the image
     */
    public function getHeight() {
        return $this->_max_x;
    }
    /**
     * setLocation
     *
     * Sets the location of the image to be recreated
     *
     * @param <type> $location the location of the image
     */
    public function setLocation($location) {
        $this->_location = $location;
        if (file_exists($this->_location)) {
            $this->imageInfo = getImageSize($this->_location);
            if ($this->imageInfo==false) {
                throw new Exception("Invalid File");
            }
        } else {
                $this->imageInfo = getImageSize($this->_location);
        }
    }
    /**
     * getImageType
     *
     * parses the imagetype and creates the new image
     *
     * @return bool if the event was successful
     */
    public function getImageType() {
        switch($this->imageInfo['mime']) {
            case 'image/gif':
                //GIF Image
                $this->image = imagecreatefromgif($this->_location);
                return true;
                break;
            case 'image/jpeg':
                //JPEG Image
                $this->image = imagecreatefromjpeg($this->_location);
                return true;
                break;
            case 'image/png':
                //PNG Image
                $this->image = imagecreatefrompng($this->_location);
                return true;
                break;
            case 'image/wbmp':
                $this->image = imagecreatefromwbmp($this->_location);
                return true;
                break;
            case 'image/bmp':
                $this->image = $this->imagecreatefrombmp($this->_location);
                if ($this->image == false) {
                    throw new Exception("Error Reading Bitmap");
                }
                return true;
                break;
            default:
                throw new Exception($this->imageInfo['mime'] . " images are not supported");
                break;
        }
        return false;
    }
    /**
     * getImageDimenstions
     *
     * gets the width and height of the image
     */
    public function getImageDimensions() {
        $this->width = $this->imageInfo[0];
        $this->height = $this->imageInfo[1];

    }
    /**
     * getImageRatios
     *
     * gets the new images scaling ratios
     */
    public function getImageRatios() {
        $this->heightRatio = $this->height/$this->_max_y*100;
        $this->widthRatio = $this->width/$this->_max_x*100;
        // selecting correct zoom factor, so that the image always keeps in the given format
        /// no matter if it is higher than wider or the other way around

        // correct the zoom aspect by choosing one of the zoom ratios
        if($this->heightRatio > $this->widthRatio) {
            $_K = $this->heightRatio;
        } else {
            $_K = $this->widthRatio;
        }
        // zoom check to the original
        if($_K > 10000/$this->_minZoom) $_K = 10000/$this->_minZoom;
        if($_K < 10000/$this->_maxZoom) $_K = 10000/$this->_maxZoom;

        // calculate new image sizes

        $this->newWidth = $this->width/$_K * 100;
        $this->newHeight = $this->height/$_K * 100;

        // correct for auto dimensions
        if($this->auto_dimension != NULL) {
            if($this->auto_dimension == "width") {
                $this->_max_x = $this->newWidth;
            } else if($this->auto_dimension == "height"){
                $this->_max_y = $this->newHeight;
            }
        }
        //throw new OSA_Exception("width:" . $this->newWidth . " Height:" . $this->newHeight);

        // set start position of the image
	// always centered
        $this->posX = ($this->_max_x-$this->newWidth) / 2;
	$this->posY = ($this->_max_y-$this->newHeight) / 2;
    }
    /**
     * createNewImage
     *
     * creates a new image in memory
     *
     */
    public function createNewImage() {
        // creating new image with given sizes
        $this->outimg = imageCreateTrueColor($this->_max_x, $this->_max_y);
        // setting colours
        $cols = explode(",", $this->picBG);
        $bgcol = imageColorallocate($this->outimg, trim($cols[0]), trim($cols[1]), trim($cols[2]));
        $cols = explode(",", $this->picFG);
        $fgcol = imageColorallocate($this->outimg, trim($cols[0]), trim($cols[1]), trim($cols[2]));

        // fill background
        imageFill($this->outimg, 0, 0, $bgcol);

        //imagecopyresampled($dst_image, $src_image, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h)
        imageCopyResampled($this->outimg, $this->image,$this->posX,$this->posY,0,0,
                $this->newWidth, $this->newHeight, $this->imageInfo[0], $this->imageInfo[1]);


    }
    /**
     * outputImage
     *
     * sends the image to the browser
     */
    public function outputImage() {
         switch($this->imageInfo['mime']) {
            case 'image/gif':
                // GIF Image
                header("Content-type: image/gif");
                imagegif($this->outimg);
                break;
            case 'image/jpeg':
                // JPEG Image
                header("Content-type: image/jpeg");
                imagejpeg($this->outimg);
                break;
            case 'image/png':
                // PNG Image
                header("Content-type: image/png");
                imagepng($this->outimg);
                break;
            case 'image/wbmp':
                header("Content-type: image/wbmp");
                imagewbmp($this->outimg);
                break;
            case 'image/bmp':
                header("Content-type: image/jpeg");
                imagejpeg($this->outimg);
                break;
        }
    }
    /**
     * destroyImage
     *
     * destroys the images in memory
     */
    public function destroyImage() {
        imagedestroy($this->image);
        imagedestroy($this->outimg);
    }
    /**
     * saveImage
     *
     * outputs the image to a file of the same type
     * (except bmp files are output as jpg files
     *
     * @param string $location the place to store the image
     */
    public function saveImage($location) {
        $filename= $this->rootpath . $location;
        $this->createDir($location);
        switch($this->imageInfo['mime']) {
            case 'image/gif':
                // GIF Image
                imagegif($this->outimg, $filename);
                break;
            case 'image/jpeg':
                // JPEG Image
                imagejpeg($this->outimg, $filename);
                break;
            case 'image/png':
                // PNG Image
                imagepng($this->outimg, $filename);
                break;
            case 'image/wbmp':
                imagewbmp($this->outimg, $filename);
                break;
            case 'image/bmp':
                imagejpeg($this->outimg, str_replace('bmp','jpg',$filename));
                break;
        }
    }
    /**
     * createDir
     *
     * verifies that the directry exists
     *
     * @param <type> $location
     */
    private function createDir($location) {
        $count = 0;
        // split the array
        $pathArry= explode("/", $location);
        // create the base
        $strpath = $this->rootpath;

        foreach($pathArry as $path) {
            $strpath .= $path."/";
            if(!is_dir($strpath)) {
                if($count < (sizeof($pathArry)-1)) {
                    mkdir($strpath);
                }
            }
            $count +=1;
        }
    }
    /**
     * createImage
     *
     * creates a new image
     *
     * @param string $image location of the image
     * @param string $save empty string or the locationa nd name of the file to
     * save to
     * @return NULL
     */
    public function createImage($image=NULL, $save='') {
        try {
            if(strlen($save)>0 &&!file_exists($save)||strlen($save)==0) {
                if ($image != NULL) {
                    // set the image location
                    $this->setLocation($image);
                }
                // determine the image type and output headers
                $this->getImageType();
                // get the image dimensions
                $this->getImageDimensions();
                // determine the scaling raios
                $this->getImageRatios();
                // build the image
                $this->createNewImage();
                // check to see if the image is to be saved
                if (strlen($save) >0  && !file_exists($save)) {
                    // save the image
                    $this->saveImage($save);
                } else {
                    // create the image
                    $this->outputImage(); 
                }
                // destroy the images
                $this->destroyImage();
            }
        } catch (Exception $e) {
            header("Content-type: image/jpeg");
            $this->image = imagecreatetruecolor($this->_max_x, $this->_max_y);
            $bgc = imagecolorallocate($this->image, 255, 255, 255);
            $tc  = imagecolorallocate($this->image, 0, 0, 0);
            imagefilledrectangle($this->image, 1, 1, $this->_max_x-2, $this->_max_y-2, $bgc);
            // Output any excptions thrown
            imagestring($this->image, $this->_max_x/100, 5, $this->_max_y/2,$e->getMessage(), $tc);
            imagejpeg($this->image);
            return;
        }
    }
    /**
     * imagecreatefrombmp
     *
     * creates an image from a bitmap file by copying the image one pixel at
     * a time
     *
     * @param string $filename
     * @return object output image
     */
    private function imagecreatefrombmp($filename) {
        // try to open the file
        if (!$f1=fopen($filename,"rb")) return false;

        // check file and read header
        $File=unpack("vfile_type/Vfile_size/Vreserved/Vbitmap_offset",fread($f1,14));
        if ($File['file_type'] != 19778) return false;

        // read entries
        $BMP = unpack('Vheader_size/Vwidth/Vheight/vplanes/vbits_per_pixel'
                                        . '/Vcompression/Vsize_bitmap/Vhoriz_resolution'
                                        . '/Vvert_resolution/Vcolors_used/Vcolors_important', fread($f1,40));
        $BMP['colors'] = pow(2,$BMP['bits_per_pixel']);
        if ($BMP['size_bitmap'] == 0) $BMP['size_bitmap'] = $FILE['file_size'] - $FILE['bitmap_offset'];
        $BMP['bytes_per_pixel'] = $BMP['bits_per_pixel']/8;
        $BMP['bytes_per_pixel2'] = ceil($BMP['bytes_per_pixel']);
        $BMP['decal'] = ($BMP['width']*$BMP['bytes_per_pixel']/4);
        $BMP['decal'] -= floor($BMP['width']*$BMP['bytes_per_pixel']/4);
        $BMP['decal'] = 4-(4*$BMP['decal']);
        if ($BMP['decal'] == 4) $BMP['decal'] = 0;

            //Change The Color pallete
            $PALETTE = array();
        if ($BMP['colors'] < 16777216){
               $PALETTE = unpack('V'.$BMP['colors'], fread($f1,$BMP['colors']*4));
        }


        //Create the image
        //$IMG = fread($f1,$BMP['size_bitmap']);
        while(!feof($f1)) {
                $IMG .= fread($f1,8192);
        }
        $VIDE = chr(0);

        $res = imagecreatetruecolor($BMP['width'],$BMP['height']);
        $P = 0;
        $Y = $BMP['height']-1;
        while ($Y >= 0) {
           $X=0;
           while ($X < $BMP['width']) {
                 if ($BMP['bits_per_pixel'] == 24) {
                        //$COLOR[1] = imagecolorallocate($res,$Y*.85,$X*.85,0);
                   $tmp = substr($IMG,$P,3);
                   switch(strlen($tmp)) {
                                case 3:
                                        $COLOR = unpack("V",$tmp.$VIDE);
                                        break;
                                case 2:
                                        $COLOR = unpack("V",$tmp.$VIDE.$VIDE);
                                        break;
                                case 1:
                                        $COLOR = unpack("V",$tmp.$VIDE.$VIDE.$VIDE);
                                        break;
                                case 0:
                                        $COLOR = unpack("V",$VIDE.$VIDE.$VIDE.$VIDE);
                                        break;
                                default:
                                         $COLOR[1] = imagecolorallocate($res,255,255,255);
                                         break;
                   }
                 } elseif ($BMP['bits_per_pixel'] == 16) {
                   $COLOR = unpack("n",substr($IMG,$P,2));
                   $COLOR[1] = $PALETTE[$COLOR[1]+1];
                 }
                 elseif ($BMP['bits_per_pixel'] == 8) {
                   $COLOR = unpack("n",$VIDE.substr($IMG,$P,1));
                   $COLOR[1] = $PALETTE[$COLOR[1]+1];
                 }
                 elseif ($BMP['bits_per_pixel'] == 4) {
                   $COLOR = unpack("n",$VIDE.substr($IMG,floor($P),1));
                   if (($P*2)%2 == 0) $COLOR[1] = ($COLOR[1] >> 4) ; else $COLOR[1] = ($COLOR[1] & 0x0F);
                   $COLOR[1] = $PALETTE[$COLOR[1]+1];
                 } elseif ($BMP['bits_per_pixel'] == 1) {
                   $COLOR = unpack("n",$VIDE.substr($IMG,floor($P),1));
                                if    (($P*8)%8 == 0) $COLOR[1] =  $COLOR[1]        >>7;
                                elseif (($P*8)%8 == 1) $COLOR[1] = ($COLOR[1] & 0x40)>>6;
                                elseif (($P*8)%8 == 2) $COLOR[1] = ($COLOR[1] & 0x20)>>5;
                                elseif (($P*8)%8 == 3) $COLOR[1] = ($COLOR[1] & 0x10)>>4;
                                elseif (($P*8)%8 == 4) $COLOR[1] = ($COLOR[1] & 0x8)>>3;
                                elseif (($P*8)%8 == 5) $COLOR[1] = ($COLOR[1] & 0x4)>>2;
                                elseif (($P*8)%8 == 6) $COLOR[1] = ($COLOR[1] & 0x2)>>1;
                                elseif (($P*8)%8 == 7) $COLOR[1] = ($COLOR[1] & 0x1);
                                $COLOR[1] = $PALETTE[$COLOR[1]+1];
                        } else
                                return FALSE;

                        if (!imagesetpixel($res,$X,$Y,$COLOR[1]))
                                return false;
                        $X++;
                        $P += $BMP['bytes_per_pixel'];
                }
                $Y--;
                $P+=$BMP['decal'];
        }

        //close the file
        fclose($f1);

        return $res;
    }
}

