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
 * @version        0.2
 */

namespace Overbid;

class ArrayAsXml
{
    private $encoding = 'UTF-8';

    private $rootName = 'root';

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
     * Set XML File Name.
     *
     * @param string
     */
    public function setFileName($fileName)
    {
        $this->fileName = $fileName;
    }

    /**
     * Main method to convert array to XML.
     *
     * @param array
     *
     * @return string
     */
    public function save($data = array())
    {
        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="'.$this->encoding.'" ?><'.$this->rootName.'></'.$this->rootName.'>');
        if ($data !== array() and is_array($data)) {
            $this->convert($data, $xml);
        }

        return html_entity_decode($xml->asXML(), ENT_NOQUOTES, $this->encoding);
    }

    /**
     * Recursive method to convert array to XML.
     *
     * @param array
     */
    private function convert($data, &$xml)
    {
        foreach ($data as $key => $value) {
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
