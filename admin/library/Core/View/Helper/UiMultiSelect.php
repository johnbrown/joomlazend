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
 * Core_View_Helper_Editor
 *
 * creates zend editor
 *
 * @author rbsolutions (rbsoultions.us@gmail.com)
 * @copyright (c) 2010 Red Black Tree LLC
 * @category ComZend
 * @package Core
 * @subpackage View_Helper
 */
class Core_View_Helper_UiMultiSelect extends Zend_View_Helper_FormElement {
    /**
     * @var string the name to use
     */
    public $name = "multisel";
    /**
     * uiMultiSelect
     *
     *
     * @param <type> $name
     * @param <type> $value
     * @param <type> $attribs
     * @return string
     */
    public function uiMultiSelect($name, $value = null, $attribs = null,
        $options = null, $listsep = "<br />\n")
    {
        $this->name = substr($name,0,strlen($name)-2);
        $this->createJavaScript();
        
        $info = $this->_getInfo($name, $value, $attribs, $options, $listsep);
        extract($info); // name, id, value, attribs, options, listsep, disable
        // force $value to array so we can compare multiple values to multiple
        // options; also ensure it's a string for comparison purposes.
        $value = array_map('strval', (array) $value);

        // check if element may have multiple values
        $multiple = '';

        if (substr($name, -2) == '[]') {
            // multiple implied by the name
            $multiple = ' multiple="multiple"';
        }

        if (isset($attribs['multiple'])) {
            // Attribute set
            if ($attribs['multiple']) {
                // True attribute; set multiple attribute
                $multiple = ' multiple="multiple"';

                // Make sure name indicates multiple values are allowed
                if (!empty($multiple) && (substr($name, -2) != '[]')) {
                    $name .= '[]';
                }
            } else {
                // False attribute; ensure attribute not set
                $multiple = '';
            }
            unset($attribs['multiple']);
        }

        // now start building the XHTML.
        $disabled = '';
        if (true === $disable) {
            $disabled = ' disabled="disabled"';
        }

        // Build the surrounding select element first.
        $xhtml = '<select'
                . ' name="' . $this->view->escape($name) . '"'
                . ' id="' . $this->view->escape($this->name) . '"'
                . $multiple
                . $disabled
                . $this->_htmlAttribs($attribs)
                . ">\n    ";

        // build the list of options
        $list       = array();
        $translator = $this->getTranslator();
        foreach ((array) $options as $opt_value => $opt_label) {
            if (is_array($opt_label)) {
                $opt_disable = '';
                if (is_array($disable) && in_array($opt_value, $disable)) {
                    $opt_disable = ' disabled="disabled"';
                }
                if (null !== $translator) {
                    $opt_value = $translator->translate($opt_value);
                }
                $list[] = '<optgroup'
                        . $opt_disable
                        . ' label="' . $this->view->escape($opt_value) .'">';
                foreach ($opt_label as $val => $lab) {
                    $list[] = $this->_build($val, $lab, $value, $disable);
                }
                $list[] = '</optgroup>';
            } else {
                $list[] = $this->_build($opt_value, $opt_label, $value, $disable);
            }
        }
        // add the options to the xhtml and close the select
        $xhtml .= implode("\n    ", $list) . "\n</select>";

        return $xhtml;
    }

    /**
     * Builds the actual <option> tag
     *
     * @param string $value Options Value
     * @param string $label Options Label
     * @param array  $selected The option value(s) to mark as 'selected'
     * @param array|bool $disable Whether the select is disabled, or individual options are
     * @return string Option Tag XHTML
     */
    protected function _build($value, $label, $selected, $disable)
    {
        if (is_bool($disable)) {
            $disable = array();
        }

        $opt = '<option'
             . ' value="' . $this->view->escape($value) . '"'
             . ' label="' . $this->view->escape($label) . '"';

        // selected?
        if (in_array((string) $value, $selected)) {
            $opt .= ' selected="selected"';
        }

        // disabled?
        if (in_array($value, $disable)) {
            $opt .= ' disabled="disabled"';
        }

        $opt .= '>' . $this->view->escape($label) . "</option>";

        return $opt;
    }
    /**
     * createJavaScript
     *
     * creates all of the javascript associated with this form
     */
    public function createJavaScript()
    {
        $view = $this->view;
        if ($this->_jQuery == NULL) {
            $this->_jQuery = new ZendX_JQuery_View_Helper_JQuery();
            $this->_jQuery->jQuery()->enable();
            
        }
        $jqHandle = ZendX_JQuery_View_Helper_JQuery::getJQueryHandler();
        // add the css
        $view->headLink()->appendStylesheet(APPLICATION_URL.'/../library/css/multiselect/common.css');
        $view->headLink()->appendStylesheet(APPLICATION_URL.'/../library/css/multiselect/ui.multiselect.css');
        // create the javascript
        ob_start();
        // ask jquery to run the plugin when it has loaded
        ?>
            <?php echo $jqHandle;?>.getScript('<?php
                echo APPLICATION_URL.'/../library/js/multiselect/plugins/scrollTo/jquery.scrollTo-min.js';?>'
                ,function() {
                <?php echo $jqHandle;?>.getScript('<?php
                    echo APPLICATION_URL.'/../library/js/multiselect/ui.multiselect.js';?>'
                    , function() {
                        <?php echo $jqHandle;?>('#<?php echo $this->name;?>').multiselect();
                });
            });
        <?php
        $js = Core_View_Compress::compressJS(ob_get_clean());
        $this->_jQuery->jQuery()->addOnLoad($js);
    }
}

