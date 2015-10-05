<?php

namespace fkooman\WebFinger;

use fkooman\WebFinger\Exception\WebFingerException;

class WebFingerData
{
    /** @var string */
    private $subject;

    /** @var array */
    private $links;

    public function __construct(array $webFingerData)
    {
        $this->subject = null;
        $this->links = array();

        // subject
        if (null !== $this->requireStringKeyValue($webFingerData, 'subject')) {
            // subject exists and is a string
            $this->subject = $webFingerData['subject'];
        }

        // links
        if (null !== $this->requireArrayKeyValue($webFingerData, 'links')) {
            // links exists and is an array
            // store the links by their rel key
            foreach ($webFingerData['links'] as $link) {
                $this->requireArray($link);
                // rel MUST be present and string
                $this->requireStringKeyValue($link, 'rel', true);
                $this->links[$link['rel']] = $link;
                // href must be string (uri)
                if (null !== $this->requireStringKeyValue($link, 'href')) {
                    $this->requireUri($link['href']);
                }
                // type must be string (media type)
                $this->requireStringKeyValue($link, 'type');
                // properties must be array
                if (null !== $this->requireArrayKeyValue($link, 'properties')) {
                    foreach ($link['properties'] as $k => $v) {
                        // key must be URI, value must be string or null
                        $this->requireUri($k);
                        $this->requireStringOrNull($v);
                    }
                }
            }
        }
    }

    public function getSubject()
    {
        return $this->subject;
    }

    public function getLinkRelations()
    {
        return array_keys($this->links);
    }

    private function getProperties($linkRelation)
    {
        if (null !== $this->requireArrayKeyValue($this->links, $linkRelation)) {
            if (null !== $this->requireArrayKeyValue($this->links[$linkRelation], 'properties')) {
                return $this->links[$linkRelation]['properties'];
            }
        }

        return;
    }

    public function getProperty($linkRelation, $property)
    {
        $properties = $this->getProperties($linkRelation);
        if (null !== $properties) {
            if (null !== $this->requireStringKeyValue($properties, $property)) {
                return $this->links[$linkRelation]['properties'][$property];
            }
        }

        return;
    }

    public function getHref($linkRelation)
    {
        if (null !== $this->requireArrayKeyValue($this->links, $linkRelation)) {
            if (null !== $this->requireStringKeyValue($this->links[$linkRelation], 'href')) {
                return $this->links[$linkRelation]['href'];
            }
        }

        return;
    }

    private static function requireArray($data)
    {
        if (!is_array($data)) {
            throw new WebFingerException('not an array');
        }
    }

    private static function requireArrayKeyValue(array $data, $key, $failWhenMissing = false)
    {
        if (array_key_exists($key, $data)) {
            if (!is_array($data[$key])) {
                throw new WebFingerException(sprintf("'%s' has not an array value", $key));
            }

            return $data[$key];
        } elseif ($failWhenMissing) {
            throw new WebFingerException(sprintf("'%s' does not exist", $key));
        }

        return;
    }

    private static function requireStringKeyValue(array $data, $key, $failWhenMissing = false)
    {
        if (array_key_exists($key, $data)) {
            if (!is_string($data[$key])) {
                throw new WebFingerException(sprintf("'%s' has not a string value", $key));
            }

            return $data[$key];
        } elseif ($failWhenMissing) {
            throw new WebFingerException(sprintf("'%s' does not exist", $key));
        }

        return;
    }

    private static function requireUri($s)
    {
        if (false === filter_var($s, FILTER_VALIDATE_URL)) {
            throw new WebFingerException('not a valid uri');
        }

        return $s;
    }

    private static function requireStringOrNull($s)
    {
        if (!is_string($s) && !is_null($s)) {
            throw new WebFingerException(sprintf("'%s' not a string or null", gettype($s)));
        }

        return $s;
    }

    public function __toString()
    {
        $output = __CLASS__.PHP_EOL;
        if (null !== $this->getSubject()) {
            $output .= sprintf("subject: %s\n", $this->getSubject());
        }
        $output .= "link relations:\n";
        foreach ($this->getLinkRelations() as $rel) {
            $output .= sprintf("  * %s\n", $rel);
            if (null !== $this->getHref($rel)) {
                $output .= sprintf("    * href: %s\n", $this->getHref($rel));
            }
            if (null !== $this->getProperties($rel)) {
                $output .= "    * properties:\n";
                foreach ($this->getProperties($rel) as $k => $v) {
                    $output .= sprintf("        * %s: %s\n", $k, $v);
                }
            }
        }

        return $output;
    }
}
