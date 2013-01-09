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
 * Core_View_Helper_QuickIcon
 *
 * creates a quick icon 
 *
 * @author rbsolutions (rbsoultions.us@gmail.com)
 * @copyright (c) 2010 Red Black Tree LLC
 * @category ComZend
 * @package Core
 * @subpackage View_Helper
 */
class Core_View_Helper_QuickIcon extends Zend_View_Helper_Abstract {
    /**
     * QuickIcon
     *
     * creates a quick Icon based on the mod_quickicon from joomla
     *
     * @param string $link
     * @param string $image
     * @param string $text
     * @param string|NULL $iconPath optional specifiy the path to the icon
     */
    public function QuickIcon($link, $image, $text, $iconPath=NULL)
    {
        $app            =& JFactory::getApplication();
        $lang		=& JFactory::getLanguage();
        $template	= $app->getTemplate();
        ?>
        <div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
            <div class="icon">
                <a href="<?php echo $link; ?>">
                    <?php  if($iconPath===NULL) {
                        echo JHTML::_('image.site',  $image, '/templates/'. $template .'/images/', NULL, NULL, $text );
                    } else {
                        echo JHTML::_('image.site',  $image, $iconPath, NULL, NULL, $text );
                    }?>
                    <span><?php echo $text; ?></span>
                </a>
            </div>
        </div>
        <?php
    }
}

