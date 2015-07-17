<?php

namespace Jeckerson;

/**
 * Array -> XML Converter Class
 * Convert array to clean XML.
 *
 * @category       Libraries
 *
 * @author         Anton Vasylyev
 *
 * @link           http://truecoder.name
 *
 * @version        1.4
 */
class Array2xml
{
    private $writer;
    private $version = '1.0';
    private $encoding = 'UTF-8';
    private $rootName = 'root';
    private $rootAttrs = array();        //example: array('first_attr' => 'value_of_first_attr', 'second_atrr' => 'etc');
    private $rootSelf = false;
    private $elementAttrs = array();        //example: $attrs['element_name'][] = array('attr_name' => 'attr_value');
    private $CDataKeys = array();
    private $newLine = "\n";
    private $newTab = "\t";
    private $numericElement = 'key';
    private $skipNumeric = true;
    private $_tabulation = true;            //TODO
    private $defaultTagName = false;    //Tag For Numeric Array Keys
    private $rawKeys = array();

    /**
     * Constructor
     * Load Standard PHP Class XMLWriter and path it to variable.
     *
     * @param array $params
     */
    public function __construct($params = array())
    {
        if (is_array($params) and !empty($params)) {
            foreach ($params as $key => $param) {
                $attr = '_'.$key;
                if (property_exists($this, $attr)) {
                    $this->$attr = $param;
                }
            }
        }

        $this->writer = new \XMLWriter();
    }

    // --------------------------------------------------------------------

    /**
     * Converter
     * Convert array data to XML. Last method to call.
     *
     * @param    array
     *
     * @return string
     */
    public function convert($data = array())
    {
        $this->writer->openMemory();
        $this->writer->startDocument($this->version, $this->encoding);
        $this->writer->startElement($this->rootName);
        if (!empty($this->rootAttrs) and is_array($this->rootAttrs)) {
            foreach ($this->rootAttrs as $rootAttrName => $rootAttrText) {
                $this->writer->writeAttribute($rootAttrName, $rootAttrText);
            }
        }

        if ($this->rootSelf === false) {
            $this->writer->text($this->newLine);

            if (is_array($data) and !empty($data)) {
                $this->_getXML($data);
            }
        }

        $this->writer->endElement();

        return $this->writer->outputMemory();
    }

    // --------------------------------------------------------------------

    /**
     * Set XML Document Version.
     *
     * @param    string
     */
    public function setVersion($version)
    {
        $this->version = (string) $version;
    }

    // --------------------------------------------------------------------

    /**
     * Set Encoding.
     *
     * @param    string
     */
    public function setEncoding($encoding)
    {
        $this->encoding = (string) $encoding;
    }

    // --------------------------------------------------------------------

    /**
     * Set XML Root Element Name.
     *
     * @param    string
     */
    public function setRootName($rootName)
    {
        $this->rootName = (string) $rootName;
    }

    // --------------------------------------------------------------------

    /**
     * Set XML Root Element Attributes.
     *
     * @param    array
     */
    public function setRootAttrs($rootAttrs)
    {
        $this->rootAttrs = (array) $rootAttrs;
    }

    // --------------------------------------------------------------------

    /**
     * Set XML Root Self close.
     *
     * @param    bool
     */
    public function setRootSelf($rootSelf)
    {
        $this->rootSelf = (bool) $rootSelf;
    }

    // --------------------------------------------------------------------

    /**
     * Set Attributes of XML Elements.
     *
     * @param    array
     */
    public function setElementsAttrs($emelentsAttrs)
    {
        $this->emelentsAttrs = (array) $emelentsAttrs;
    }

    // --------------------------------------------------------------------

    /**
     * Set keys of array that needed to be as CData in XML document.
     *
     * @param    array
     */
    public function setCDataKeys(array $CDataKeys)
    {
        $this->CDataKeys = $CDataKeys;
    }

    // --------------------------------------------------------------------

    /**
     * Set keys of array that needed to be as Raw XML in XML document.
     *
     * @param     array
     */
    public function setRawKeys(array $rawKeys)
    {
        $this->rawKeys = $rawKeys;
    }

    // --------------------------------------------------------------------

    /**
     * Set New Line.
     *
     * @param    string
     */
    public function setNewLine($newLine)
    {
        $this->newLine = (string) $newLine;
    }

    // --------------------------------------------------------------------

    /**
     * Set New Tab.
     *
     * @param    string
     */
    public function setNewTab($newTab)
    {
        $this->newTab = (string) $newTab;
    }

    // --------------------------------------------------------------------

    /**
     * Set Default Numeric Element.
     *
     * @param    string
     */
    public function setNumericElement($numericElement)
    {
        $this->numericElement = (string) $numericElement;
    }

    // --------------------------------------------------------------------

    /**
     * On/Off Skip Numeric Array Keys.
     *
     * @param    string
     */
    public function setSkipNumeric($skipNumeric)
    {
        $this->skipNumeric = (bool) $skipNumeric;
    }

    // --------------------------------------------------------------------

    /**
     * Tag For Numeric Array Keys.
     *
     * @param    string
     */
    public function setDefaultTagName($defaultTagName)
    {
        $this->defaultTagName = (string) $defaultTagName;
    }

    // --------------------------------------------------------------------

    /**
     * Writing XML document by passing through array.
     *
     * @param    array
     * @param    int
     */
    private function _getXML(&$data, $tabs_count = 0)
    {
        foreach ($data as $key => $val) {
            unset($data[$key]);

            // Skip attribute param
            if (substr($key, 0, 1) == '@') {
                continue;
            }

            if (is_numeric($key) && $this->defaultTagName !== false) {
                $key = $this->defaultTagName;
            } elseif (is_numeric($key)) {
                if ($this->skipNumeric === true) {
                    if (!is_array($val)) {
                        $tabs_count = 0;
                    } else {
                        if ($tabs_count > 0) {
                            --$tabs_count;
                        }
                    }

                    $key = false;
                } else {
                    $key = $this->numericElement.$key;
                }
            }

            if ($key !== false) {
                $this->writer->text(str_repeat($this->newTab, $tabs_count));

                // Write element tag name
                $this->writer->startElement($key);

                // Check if there are some attributes
                if (isset($this->elementAttrs[$key]) || isset($val['@attributes'])) {
                    if (isset($val['@attributes']) && is_array($val['@attributes'])) {
                        $attributes = $val['@attributes'];
                    } else {
                        $attributes = $this->elementAttrs[$key];
                    }

                    // Yeah, lets add them
                    foreach ($attributes as $elementAttrName => $elementAttrText) {
                        $this->writer->startAttribute($elementAttrName);
                        $this->writer->text($elementAttrText);
                        $this->writer->endAttribute();
                    }
                }
            }

            if (is_array($val)) {
                if ($key !== false) {
                    $this->writer->text($this->newLine);
                }

                ++$tabs_count;
                $this->_getXML($val, $tabs_count);
                --$tabs_count;

                if ($key !== false) {
                    $this->writer->text(str_repeat($this->newTab, $tabs_count));
                }
            } else {
                if ($val != null || $val === 0) {
                    if (isset($this->CDataKeys[$key])) {
                        $this->writer->writeCData($val);
                    } elseif (isset($this->rawKeys[$key])) {
                        $this->writer->writeRaw($val);
                    } else {
                        $this->writer->text($val);
                    }
                }
            }

            if ($key !== false) {
                $this->writer->endElement();
                $this->writer->text($this->newLine);
            }
        }
    }
}
//END Array to Xml Class

