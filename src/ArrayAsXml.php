<?php

/**
 * Array as XML Convert Class
 * Convert array to XML.
 *
 * @category       Libraries
 *
 * @author         Eakkapat Pattarathamrong
 *
 * @link           https://github.com/overbid/arrayasxml
 *
 * @version        0.1
 */

namespace Overbid;

class ArrayAsXml
{
    private $version = '1.0';

    private $encoding = 'UTF-8';

    private $rootName = 'root';

    /**
     * Set XML Document Version.
     *
     * @param string
     */
    public function setVersion($version)
    {
        $this->version = (string) $version;
    }

    /**
     * Set Encoding.
     *
     * @param string
     */
    public function setEncoding($encoding)
    {
        $this->encoding = (string) $encoding;
    }

    /**
     * Set XML Root Element Name.
     *
     * @param string
     */
    public function setRootName($rootName)
    {
        $this->rootName = (string) $rootName;
    }

    /**
     * Main method to convert array to XML.
     *
     * @param array
     *
     * @return string
     */
    public function asXml($data = array())
    {
        $xml = new \SimpleXMLElement('<?xml version="'.$this->version.'" encoding="'.$this->encoding.'" ?><'.$this->rootName.'></'.$this->rootName.'>');
        if ($data !== array() and is_array($data)) {
            $this->convert($data, $xml);
        }

        return $xml->asXML();
    }

    /**
     * Recursive method to convert array to XML.
     *
     * @param array
     */
    private function convert($array, &$xml)
    {
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                if (!is_numeric($key)) {
                    $leaf = $xml->addChild("$key");
                    $this->convert($value, $leaf);
                } else {
                    $leaf = $xml->addChild("item$key");
                    $this->convert($value, $leaf);
                }
            } else {
                $xml->addChild("$key", htmlspecialchars("$value"));
            }
        }
    }
}
