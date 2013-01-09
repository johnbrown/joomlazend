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
 * Description of page
 *
 * tools to help create a pdf document
 *
 * @author rbsolutions (rbsoultions.us@gmail.com)
 * @copyright (c) 2010 Red Black Tree LLC
 * @category ComZend
 * @package Core
 * @subpackage View_Pdf
 */
class Core_View_Pdf_Page extends Zend_Pdf_Page {
 /**
     * The default encoding
     *
     * @var string
     */
    public static $encoding = 'UTF-8';

    /**
     * Align text at left of provided coordinates
     *
     * @var string
     */
    const TEXT_ALIGN_LEFT = 'left';

    /**
     * Align text at right of provided coordinates
     *
     * @var string
     */
    const TEXT_ALIGN_RIGHT = 'right';

    /**
     * Center-text horizontally within provided coordinates
     *
     * @var string
     */
    const TEXT_ALIGN_CENTER = 'center';
    /**
     * Align text at top of provided coordinates
     *
     * @var string
     */
    const TEXT_ALIGN_TOP = 'top';
    /**
     * drawTextBox
     * 
     * draws a text box based on height and width from the top left
     * 
     * @param type $text
     * @param type $top
     * @param type $left
     * @param type $width
     * @param type $height
     * @param type $position
     * @param type $encoding
     * @param type $font
     * @return type 
     */
    public function drawTextBoxHW($text, $top, $left, $width, $height, 
            $hPosition = self::TEXT_ALIGN_LEFT,
            $vPosition = self::TEXT_ALIGN_TOP, $lineHeightMultiplier = 1.1,
            $encoding = null, $font = NULL)
    {
        $bottom = $top+$height;
        $right = $left +$width;
        $x1 = $left;
        $y1 = $this->getHeight() - $top;
        $x2 = $x1 + $width;
        $y2 = $y1 - $height;
        $this->drawTextBox($this, $text, $x1, $y1, $x2, $y2, $hPosition, 
                $vPosition, $lineHeightMultiplier, $encoding, $font);
        return $this;
    }
    /**
     * drawBox
     * 
     * draws a filled in box
     * 
     * @param type $top
     * @param type $left
     * @param type $width
     * @param type $height
     * @param type $fillType
     * @return type 
     */
    public function drawBoxHW($top, $left, $width, $height, 
            $fillType = self::SHAPE_DRAW_FILL_AND_STROKE)
    {
        $bottom = $top+$height;
        $right = $left +$width;
        $x1 = $right;
        $y1 = $this->getHeight() - $top;
        $x2 = $x1 - $width;
        $y2 = $y1 - $height;
        $this->drawRectangle($x2, $y2, $x1, $y1, $fillType);
        return $this;
    }
    /**
     * drawRoundedBox
     * 
     * draws a rounded rectangle
     * 
     * @param type $top
     * @param type $left
     * @param type $width
     * @param type $height
     * @param type $radius
     * @param type $fillType
     * @return type 
     */
    public function drawRoundedBoxHW($top, $left, $width, $height, $radius = 0,
            $fillType = self::SHAPE_DRAW_FILL_AND_STROKE)
    {
        $bottom = $top+$height;
        $right = $left +$width;
        $x1 = $right;
        $y1 = $this->getHeight() - $top;
        $x2 = $x1 - $width;
        $y2 = $y1 - $height;
        $this->drawRoundedRectangle($x2, $y2, $x1, $y1, $radius, $fillType);
        return $this;
    }
    /**
     * drawText2
     * static function
     * 
     * Extension of basic draw-text function to allow it to vertically center text
     *
     * @param Zend_Pdf_Page $page
     * @param string $text
     * @param int $x1
     * @param int $y1
     * @param int $x2
     * @param int $position
     * @param string $encoding
     * @return Zend_Pdf_Page
     */
    public static function drawText2(Zend_Pdf_Page &$page, $text, $x1, $y1, $x2 = null,
            $position = self::TEXT_ALIGN_LEFT, $encoding = NULL,$font = NULL)
    {
        if($font!= NULL) {
            $fontSize=$page->getFontSize();
            $page->setFont($font, $fontSize);
        }
        if( $encoding == null ) $encoding = self::$encoding;

        switch ($position) {
            case self::TEXT_ALIGN_LEFT :
                $left = $x1;
                break;
            case self::TEXT_ALIGN_RIGHT :
            if (null === $x2) {
                    throw new Exception ( "Cannot right-align text horizontally, x2 is not provided" );
                }
                $textWidth = self::getTextWidth ( $text, $page );
                $left = $x2 - $textWidth;
                break;
            case self::TEXT_ALIGN_CENTER :
                if (null === $x2) {
                    throw new Exception ( "Cannot center text horizontally, x2 is not provided" );
                }
                $textWidth = self::getTextWidth ( $text, $page );
                $left = $x1 + ($x2-$x1)/2- $textWidth / 2;
                break;
            default :
                throw new Exception ( "Invalid position value \"$position\"" );
        }

        // display multi-line text

        $page->drawText ( self::decode_entities_full($text,ENT_COMPAT, 'utf-8'), $left, $y1, $encoding );
        return $page;
    }
    /**
     * decodes html entities
     *
     * static call
     *
     * @param <type> $string
     * @param <type> $quotes
     * @param <type> $charset
     * @return <type>
     */
    public static function decode_entities_full($string,$quotes=ENT_COMPAT,$charset='ISO-8859-1') {
        return //utf8_encode(
                html_entity_decode(
                    preg_replace_callback(
                            '/&([a-zA-Z][a-zA-Z0-9]+);/',
                            'Core_View_Pdf_Page::convert_entity',
                            $string),
                    $quotes,
                    $charset
                );
            //);
    }
    /**
     * convert_entity
     * static call
     *
     * converts an HTML entity to its proper char
     *
     * @staticvar array $table
     * @param <type> $matches
     * @param <type> $destroy
     * @return string
     */
    public static function convert_entity($matches,$destroy=true)
    {
        static $table = array(
            'quot' => '&#34;','amp' => '&#38;','lt' => '&#60;','gt' => '&#62;',
            'OElig' => '&#338;','oelig' => '&#339;','Scaron' => '&#352;',
            'scaron' => '&#353;','Yuml' => '&#376;','circ' => '&#710;',
            'tilde' => '&#732;','ensp' => '&#8194;','emsp' => '&#8195;',
            'thinsp' => '&#8201;','zwnj' => '&#8204;','zwj' => '&#8205;','lrm' => '&#8206;',
            'rlm' => '&#8207;','ndash' => '&#8211;','mdash' => '&#8212;','lsquo' => '&#8216;',
            'rsquo' => '&#8217;','sbquo' => '&#8218;','ldquo' => '&#8220;',
            'rdquo' => '&#8221;','bdquo' => '&#8222;','dagger' => '&#8224;',
            'Dagger' => '&#8225;','permil' => '&#8240;','lsaquo' => '&#8249;',
            'rsaquo' => '&#8250;','euro' => '&#8364;','fnof' => '&#402;',
            'Alpha' => '&#913;','Beta' => '&#914;','Gamma' => '&#915;',
            'Delta' => '&#916;','Epsilon' => '&#917;','Zeta' => '&#918;',
            'Eta' => '&#919;','Theta' => '&#920;','Iota' => '&#921;',
            'Kappa' => '&#922;','Lambda' => '&#923;','Mu' => '&#924;',
            'Nu' => '&#925;','Xi' => '&#926;','Omicron' => '&#927;',
            'Pi' => '&#928;','Rho' => '&#929;','Sigma' => '&#931;',
            'Tau' => '&#932;','Upsilon' => '&#933;','Phi' => '&#934;',
            'Chi' => '&#935;','Psi' => '&#936;','Omega' => '&#937;',
            'alpha' => '&#945;','beta' => '&#946;','gamma' => '&#947;',
            'delta' => '&#948;','epsilon' => '&#949;','zeta' => '&#950;',
            'eta' => '&#951;','theta' => '&#952;','iota' => '&#953;',
            'kappa' => '&#954;','lambda' => '&#955;','mu' => '&#956;','nu' => '&#957;',
            'xi' => '&#958;','omicron' => '&#959;','pi' => '&#960;',
            'rho' => '&#961;','sigmaf' => '&#962;','sigma' => '&#963;',
            'tau' => '&#964;','upsilon' => '&#965;','phi' => '&#966;',
            'chi' => '&#967;','psi' => '&#968;','omega' => '&#969;',
            'thetasym' => '&#977;','upsih' => '&#978;','piv' => '&#982;',
            'bull' => '&#8226;','hellip' => '&#8230;','prime' => '&#8242;',
            'Prime' => '&#8243;','oline' => '&#8254;','frasl' => '&#8260;',
            'weierp' => '&#8472;','image' => '&#8465;','real' => '&#8476;',
            'trade' => '&#8482;','alefsym' => '&#8501;','larr' => '&#8592;',
            'uarr' => '&#8593;','rarr' => '&#8594;','darr' => '&#8595;',
            'harr' => '&#8596;','crarr' => '&#8629;','lArr' => '&#8656;','uArr' => '&#8657;',
            'rArr' => '&#8658;','dArr' => '&#8659;','hArr' => '&#8660;','forall' => '&#8704;',
            'part' => '&#8706;','exist' => '&#8707;','empty' => '&#8709;',
            'nabla' => '&#8711;','isin' => '&#8712;','notin' => '&#8713;',
            'ni' => '&#8715;','prod' => '&#8719;','sum' => '&#8721;',
            'minus' => '&#8722;','lowast' => '&#8727;','radic' => '&#8730;',
            'prop' => '&#8733;','infin' => '&#8734;','ang' => '&#8736;',
            'and' => '&#8743;','or' => '&#8744;','cap' => '&#8745;',
            'cup' => '&#8746;','int' => '&#8747;','there4' => '&#8756;',
            'sim' => '&#8764;','cong' => '&#8773;','asymp' => '&#8776;',
            'ne' => '&#8800;','equiv' => '&#8801;','le' => '&#8804;',
            'ge' => '&#8805;','sub' => '&#8834;','sup' => '&#8835;',
            'nsub' => '&#8836;','sube' => '&#8838;','supe' => '&#8839;',
            'oplus' => '&#8853;','otimes' => '&#8855;','perp' => '&#8869;',
            'sdot' => '&#8901;','lceil' => '&#8968;','rceil' => '&#8969;',
            'lfloor' => '&#8970;','rfloor' => '&#8971;','lang' => '&#9001;',
            'rang' => '&#9002;','loz' => '&#9674;','spades' => '&#9824;',
            'clubs' => '&#9827;','hearts' => '&#9829;','diams' => '&#9830;',
            'nbsp' => '&#160;','iexcl' => '&#161;','cent' => '&#162;',
            'pound' => '&#163;','curren' => '&#164;','yen' => '&#165;',
            'brvbar' => '&#166;','sect' => '&#167;','uml' => '&#168;',
            'copy' => '&#169;','ordf' => '&#170;','laquo' => '&#171;',
            'not' => '&#172;','shy' => '&#173;','reg' => '&#174;',
            'macr' => '&#175;','deg' => '&#176;','plusmn' => '&#177;',
            'sup2' => '&#178;','sup3' => '&#179;','acute' => '&#180;',
            'micro' => '&#181;','para' => '&#182;','middot' => '&#183;',
            'cedil' => '&#184;','sup1' => '&#185;','ordm' => '&#186;',
            'raquo' => '&#187;','frac14' => '&#188;','frac12' => '&#189;',
            'frac34' => '&#190;','iquest' => '&#191;','Agrave' => '&#192;',
            'Aacute' => '&#193;','Acirc' => '&#194;','Atilde' => '&#195;',
            'Auml' => '&#196;','Aring' => '&#197;','AElig' => '&#198;',
            'Ccedil' => '&#199;','Egrave' => '&#200;','Eacute' => '&#201;',
            'Ecirc' => '&#202;','Euml' => '&#203;','Igrave' => '&#204;',
            'Iacute' => '&#205;','Icirc' => '&#206;','Iuml' => '&#207;',
            'ETH' => '&#208;','Ntilde' => '&#209;','Ograve' => '&#210;',
            'Oacute' => '&#211;','Ocirc' => '&#212;','Otilde' => '&#213;',
            'Ouml' => '&#214;','times' => '&#215;','Oslash' => '&#216;',
            'Ugrave' => '&#217;','Uacute' => '&#218;','Ucirc' => '&#219;',
            'Uuml' => '&#220;','Yacute' => '&#221;','THORN' => '&#222;',
            'szlig' => '&#223;','agrave' => '&#224;','aacute' => '&#225;',
            'acirc' => '&#226;','atilde' => '&#227;','auml' => '&#228;',
            'aring' => '&#229;','aelig' => '&#230;','ccedil' => '&#231;',
            'egrave' => '&#232;','eacute' => '&#233;','ecirc' => '&#234;',
            'euml' => '&#235;','igrave' => '&#236;','iacute' => '&#237;',
            'icirc' => '&#238;','iuml' => '&#239;','eth' => '&#240;',
            'ntilde' => '&#241;','ograve' => '&#242;','oacute' => '&#243;',
            'ocirc' => '&#244;','otilde' => '&#245;','ouml' => '&#246;',
            'divide' => '&#247;','oslash' => '&#248;','ugrave' => '&#249;',
            'uacute' => '&#250;','ucirc' => '&#251;','uuml' => '&#252;',
            'yacute' => '&#253;','thorn' => '&#254;','yuml' => '&#255;',
        );
        if(isset($table[$matches[1]])) return $table[$matches[1]];

        return $destroy?'':$matches[0];
    }
    /**
     * checkFont
     *
     * looks for font changes
     *
     * @param string $word
     * @param string $currentFont
     */
    public static function checkFont($word, $currentFont="standard")
    {
        $action = 0;
        if(substr($word,0,6)=="<b><i>") {
            $currentFont = 'bold_italic';
            $word = substr($word,6);
            $action=1;
        } else if(substr($word,0,3) =='<b>') {
            switch($currentFont) {
                case "italic":
                    $currentFont = "bold_italic";
                    break;
                case "standard":
                    $currentFont = "bold";
                    break;
                default:
                    break;
            }
            $word = substr($word,3);
            $action=1;
        } else if(substr($word,0,3) =='<i>') {
            switch($currentFont) {
                case "bold":
                    $currentFont = "bold_italic";
                    break;
                case "standard":
                    $currentFont = "italic";
                    break;
                default:
                    break;
            }
            $action=1;
            $word = substr($word,3);
        } else if(substr($word,strlen($word)-8)=="</b></i>") {
            $currentFont = 'standard';
            $word = substr($word,0,strlen($word)-8);
        } else if(substr($word,strlen($word)-4) =='</b>') {
            switch($currentFont) {
                case "bold_italic":
                    $currentFont = "italic";
                    break;
                case "bold":
                    $currentFont = "standard";
                    break;
                default:
                    break;
            }
            $word = substr($word,0,strlen($word)-4);
        } else if(substr($word,strlen($word)-4) =='</i>') {
            switch($currentFont) {
                case "bold_italic":
                    $currentFont = "bold";
                    break;
                case "italic":
                    $currentFont = "standard";
                    break;
                default:
                    break;
            }
            $word = substr($word,0,strlen($word)-4);
        } else if(substr($word,strlen($word)-9)=="</b></i> ") {
            $currentFont = 'standard';
            $word = substr($word,0,strlen($word)-9).' ';
        } else if(substr($word,strlen($word)-5) =='</b> ') {
            switch($currentFont) {
                case "bold_italic":
                    $currentFont = "italic";
                    break;
                case "bold":
                    $currentFont = "standard";
                    break;
                default:
                    break;
            }
            $word = substr($word,0,strlen($word)-5).' ';
        } else if(substr($word,strlen($word)-5) =='</i> ') {
            switch($currentFont) {
                case "bold_italic":
                    $currentFont = "bold";
                    break;
                case "italic":
                    $currentFont = "standard";
                    break;
                default:
                    break;
            }
            $word = substr($word,0,strlen($word)-5). ' ';
        }
        return array(
            'font'=>$currentFont,
            'action'=>$action,
            'word'=>$word,
            );
    }
    /**
     * Draw text inside a box using word wrap
     *
     * @param Zend_Pdf_Page $page
     * @param string $text
     * @param int $x1
     * @param int $y1
     * @param int $x2
     * @param int $position
     * @param float $lineHeightMultiplier
     * @param string $encoding
     *
     * @return integer bottomPosition
     */
    public static function drawTextBox(Zend_Pdf_Page &$page, $text,  $x1, $y1,
            $x2 = null, $y2 = NULL, $hPosition = self::TEXT_ALIGN_LEFT,
            $vPosition = self::TEXT_ALIGN_TOP, $lineHeightMultiplier = 1.1,
            $encoding = null, $font = NULL)
    {
        if( $encoding == null ) $encoding = self::$encoding;
        // default the font if it is not specified
        if($font==NULL) {
            $font =array(
                'standard'=>$page->getFont(),
                'bold'=>$page->getFont(),
                'italic'=>$page->getFont(),
                'bold_italic'=>$page->getFont(),
            );
        }
        $currentFont = 'standard';
        $lines = explode(PHP_EOL, $text);
        $lineHeight = $page->getFontSize() * $lineHeightMultiplier;

        switch($vPosition) {
            case self::TEXT_ALIGN_TOP:
                $bottom = $y1 - $page->getFontSize();
                break;
            case self::TEXT_ALIGN_CENTER:
                $bottom = $y1 + ($y2-$y1)/2 - ($lineHeight)/4;
                break;
            default:
                $bottom = $y1 - $page->getFontSize();
                break;
        }

        $resultArray = array();
        $nextFont=NULL;
        $numLines = 0;
        foreach( $lines as $line ){
            preg_match_all('/([^\s]*\s*)/i', $line, $matches);
            $tmpX1 = $x1;
            $words = $matches[1];
            $lineText = '';
            $lineWidth = 0;
            foreach( $words as $word ){
                if(strlen($word)>0) {
                    $newFontArray = self::checkFont($word,$currentFont);
                    $newFont = $newFontArray['font'];
                    $word = $newFontArray['word'];
                    //var_dump($word);
                    $wordWidth = self::getTextWidth($word, $page);
                    if($nextFont!= NULL) {
                        //echo "Clearing font to:".$nextFont."<br />";
                        $currentFont = $nextFont;
                        $newFont = $currentFont;
                        $nextFont = NULL;
                        if($lineText== ' ') {
                            $tmpX1 += $wordWidth+$lineWidth+$lineLen+self::getTextWidth(" ",$page);
                        }
                    }
                    if($newFont != $currentFont) {
                        // change in font
                        if( $lineWidth+$wordWidth < $x2-$x1 ){
                            if($newFontArray['action'] == 1) {
                                //echo "setting new font:".$newFont."<br />";
                                $currentFont = $newFont;
                            }
                            // we are on the same line but changing fonts
                            $resultArray[] = array(
                                'page'=>$page,
                                'lineText'=>trim($lineText.$word),
                                'x1'=>$tmpX1,
                                'bottom'=>$bottom,
                                'x2'=>$x2,
                                'position'=>self::TEXT_ALIGN_LEFT,
                                'encoding'=>$encoding,
                                'font'=>$font[$currentFont],
                            );
                            $tmpX1 = $x1 + $lineWidth+$wordWidth;
                            $lineLen = self::getTextWidth($lineText.$word." ",$page);
                            $lineText=' ';
                            $lineWidth = self::getTextWidth(' ', $page);
                        }
                        if($newFontArray['action'] == 0) {
                            $nextFont = $newFont;
                            //echo "setting next font:".$newFont."<br />";
                        }
                    } else if( $lineWidth+$wordWidth < $x2-$x1 ){
                        // same line same font
                        $lineText .= $word;
                        $lineWidth += $wordWidth;
                    }else{
                        $numLines++;
                        // new line
                        if($vPosition == self::TEXT_ALIGN_CENTER) {
                            $bottom += $lineHeight;
                        }
                        // add the existing line
                        $resultArray[] = array(
                            'page'=>$page,
                            'lineText'=>trim($lineText),
                            'x1'=>$tmpX1,
                            'bottom'=>$bottom,
                            'x2'=>$x2,
                            'position'=>$hPosition,
                            'encoding'=>$encoding,
                            'font'=>$font[$currentFont],
                        );
                        if($vPosition == self::TEXT_ALIGN_CENTER) {
                            $bottom -= $lineHeight*2;
                        } else{
                            $bottom -= $lineHeight;
                        }
                        $lineText = $word;
                        $lineWidth = $wordWidth;
                        $tmpX1 = $x1;
                    }
                }
            }
            $resultArray[] = array(
                        'page'=>$page,
                        'lineText'=>trim($lineText),
                        'x1'=>$tmpX1,
                        'bottom'=>($vPosition == self::TEXT_ALIGN_CENTER)?$bottom+$lineHeight:$bottom,
                        'x2'=>$x2,
                        'position'=>$hPosition,
                        'encoding'=>$encoding,
                        'font'=>$font[$currentFont],
                    );
            $bottom -= $lineHeight;
        }

        // check to see if its too tall
        $maxHeight = $y1-$y2;
        if($lineHeight*sizeof($resultArray) >= $maxHeight && $page->getFontSize() >0) {
            $font = $page->getFont();
            $page->setFont($font, $page->getFontSize()-1);
            return self::drawTextBox($page, $text, $x1, $y1, $x2, $y2, $hPosition, $vPosition, $lineHeightMultiplier, $encoding);
        }
        foreach($resultArray as $r) {
            self::drawText2( $r['page'], $r['lineText'], $r['x1'],
                    $r['bottom'], $r['x2'], $r['position'], $r['encoding'], $r['font'] );
        }

        return $bottom;
    }

    /**
     * Return length of generated string in points
     *
     * @param string                     $text
     * @param Zend_Pdf_Resource_Font|Zend_Pdf_Page     $font
     * @param int                         $fontSize
     * @return double
     */
    public static function getTextWidth($text, $resource, $fontSize = null, $encoding = null) {
        if( $encoding == null ) $encoding = self::$encoding;

        if( $resource instanceof Zend_Pdf_Page ){
            $font = $resource->getFont();
            $fontSize = $resource->getFontSize();
        }elseif( $resource instanceof Zend_Pdf_Resource_Font ){
            $font = $resource;
            if( $fontSize === null ) throw new Exception('The fontsize is unknown');
        }

        if( !$font instanceof Zend_Pdf_Resource_Font ){
            throw new Exception('Invalid resource passed');
        }

        $drawingText = iconv ( '', $encoding, $text );
        $characters = array ();
        for($i = 0; $i < strlen ( $drawingText ); $i ++) {
            $characters [] = ord ( $drawingText [$i] );
        }
        $glyphs = $font->glyphNumbersForCharacters ( $characters );
        $widths = $font->widthsForGlyphs ( $glyphs );
        $textWidth = (array_sum ( $widths ) / $font->getUnitsPerEm ()) * $fontSize;
        return $textWidth;
    }
    /**
     * paddedTextBox
     *
     * creates a padded text box
     *
     * @param Zend_Pdf_Page $page
     * @param string $text
     * @param <type> $bgColor
     * @param <type> $fgColor
     * @param <type> $x1
     * @param <type> $y1
     * @param <type> $x2
     * @param <type> $y2
     * @param <type> $padding
     * @param <type> $hPosition
     * @param <type> $vPosition
     * @param <type> $lineHeightMultiplier
     * @param <type> $encoding
     */
    public static function paddedTextBox(Zend_Pdf_Page &$page, $text, $bgColor, $fgColor,
            $x1, $y1, $x2=NULL, $y2=NULL, $padding=0, $hPosition = self::TEXT_ALIGN_LEFT,
            $vPosition = self::TEXT_ALIGN_TOP, $lineHeightMultiplier = 1.1,
            $encoding = null, $font=NULL)
    {

        // create the title box
        $page->setFillColor($bgColor)
                ->setLineColor($bgColor)
                ->drawRectangle(
                $x1,
                $y1,
                $x2,
                $y2,
                self::SHAPE_DRAW_FILL_AND_STROKE);
        $page->setFillColor($fgColor);
        self::drawTextBox(
                $page,
                $text,
                $x1+$padding,
                $y1-$padding,
                $x2-$padding,
                $y2+$padding,
                $hPosition,
                $vPosition,
                $lineHeightMultiplier,
                $encoding,
                $font
            );
    }
    /**
     * paddedImage
     * static call
     *
     * adds an image with padding to the document
     *
     * @param Zend_Pdf_Page $page
     * @param string $imagePath local file path to the image
     * @param Zend_pdf_color $bgColor background color
     * @param Zend_pdf_color $borderColor border color
     * @param int $x
     * @param int $y
     * @param int $width
     * @param int $height
     * @param int $padding
     * @param <type> $fillType
     */
    public static function paddedImage(Zend_Pdf_Page &$page, $imagePath, $bgColor,
            $borderColor, $x, $y, $width, $height, $padding, $fillType=self::SHAPE_DRAW_FILL_AND_STROKE)
    {
        //echo $imagePath;
        $img = new Core_View_Image();
        $img->setWidth($width*2);
        $img->setHeight($height*2);
        $nImageName = substr($imagePath,0,strlen($imagePath)-4)."_pdf".substr($imagePath,-4);
        $imgSrc = $img->createImage($imagePath, $nImageName);
        unset($img);
        $image = Zend_Pdf_Image::imageWithPath($nImageName);
        //echo "Image loaded:".memory_get_usage()."\n";
        $page->setFillColor($bgColor)
                ->setLineColor($borderColor)
                ->drawRectangle(
                $x-$padding,
                $y - $padding,
                $x+$width+$padding,
                $y+$height+$padding,
                $fillType
            );
        $page->drawImage(
                $image,
                $x,
                $y,
                ($x+$width),
                ($y+$width)
            );
        unset($image);
    }
}

