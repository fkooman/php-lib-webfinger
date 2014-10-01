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
        if (null !== $this->requireStringKeyValue($webFingerData, "subject")) {
            // subject exists and is a string
            $this->subject = $webFingerData['subject'];
        }

        // links
        if (null !== $this->requireArrayKeyValue($webFingerData, "links")) {
            // links exists and is an array
            // store the links by their rel key
            foreach ($webFingerData['links'] as $link) {
                $this->requireArray($link);
                // rel MUST be present
                $this->requireStringKeyValue($link, 'rel', true);
                $this->links[$link['rel']] = $link;
                // href must be string
                if (null !== $this->requireStringKeyValue($link, 'href')) {
                    $this->requireUri($link['href']);
                }
                // type must be string
                $this->requireStringKeyValue($link, 'type');
                // properties must be array
                if (null !== $this->requireArrayKeyValue($link, 'properties')) {
                    foreach ($link['properties'] as $k => $v) {
                        // key must be URI, value must be string or null
                        $this->requireUri($k);
                        $this->requireStringOrNull($v);
                    }
                }
                $this->validateLinkRelation($link['rel']);
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

    public function hasLinkRelation($rel)
    {
        return array_key_exists($rel, $this->links);
    }

    public function getProperties($rel)
    {
        if (null !== $this->requireArrayKeyValue($this->links, $rel)) {
            $this->requireArrayKeyValue($this->links[$rel], 'properties');

            return $this->links[$rel]['properties'];
        }

        return null;
    }

    public function getHref($rel)
    {
        if (null !== $this->requireArrayKeyValue($this->links, $rel, true)) {
            $this->requireStringKeyValue($this->links[$rel], 'href');

            return $this->links[$rel]['href'];
        }

        return null;
    }

    private static function requireArray($data)
    {
        if (!is_array($data)) {
            throw new WebFingerException("not an array");
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

        return null;
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

        return null;
    }

    private static function requireUri($s)
    {
        if (false === filter_var($s, FILTER_VALIDATE_URL)) {
            throw new WebFingerException("not a valid uri");
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

    private function validateLinkRelation($linkRelation)
    {
        // additional checks for remotestorage link relation
        if ("remotestorage" === $linkRelation) {
            // needs href
            if (null === $this->getHref($linkRelation)) {
                throw new WebFingerException("remotestorage needs 'href'");
            }
            // needs properties
            $properties = $this->getProperties($linkRelation);
            if (null === $properties) {
                throw new WebFingerException("remotestorage needs 'properties'");
            }

            // needs authUri property
            $this->requireStringKeyValue($properties, 'http://tools.ietf.org/html/rfc6749#section-4.2', true);
            $this->requireUri($properties['http://tools.ietf.org/html/rfc6749#section-4.2']);

            // needs version property
            $this->requireStringKeyValue($properties, 'http://remotestorage.io/spec/version', true);

            // optional properties
            if (null !== $this->requireStringKeyValue($properties, 'https://tools.ietf.org/html/rfc2616#section-14.16')) {
                if (!in_array($properties['https://tools.ietf.org/html/rfc2616#section-14.16'], array("true", "false"))) {
                    throw new WebFingerException("'property needs to be 'true' or 'false' as string");
                }
            }
            if (null !== $this->requireStringKeyValue($properties, 'http://tools.ietf.org/html/rfc6750#section-2.3')) {
                if (!in_array($properties['http://tools.ietf.org/html/rfc6750#section-2.3'], array("true", "false"))) {
                    throw new WebFingerException("'property needs to be 'true' or 'false' as string");
                }
            }
        }
    }

    public function __toString()
    {
        $output = __CLASS__ . PHP_EOL;
        if (null !== $this->getSubject()) {
            $output .= sprintf("Subject: %s\n", $this->getSubject());
        }
        $output .= "Link Relations:\n";
        foreach ($this->getLinkRelations() as $rel) {
            $output .= sprintf("  * %s\n", $rel);
            if (null !== $this->getHref($rel)) {
                $output .= sprintf("      Href: %s\n", $this->getHref($rel));
            }
            if (null !== $this->getProperties($rel)) {
                $output .= "      Properties:\n";
                foreach ($this->getProperties($rel) as $k => $v) {
                    $output .= sprintf("        * %s: %s\n", $k, $v);
                }
            }
        }

        return $output;
    }
}
