<?php

namespace fkooman\WebFinger;

use fkooman\WebFinger\Exception\WebFingerException;

class RemoteStorageWebFinger extends WebFinger
{
    // validate the RemoteStorage "links" section
    protected function validateData(array $webFingerData)
    {
        parent::validateData($webFingerData);

        // remoteStorage needs a "links" section
        if (!array_key_exists("links", $webFingerData) || !is_array($webFingerData['links'])) {
            throw new WebFingerException("missing links section or malformed");
        }

        foreach ($webFingerData['links'] as $link) {
            // look for "rel" "remoteStorage"
            if (!array_key_exists("rel", $link) || !is_string($link['rel'])) {
                throw new WebFingerException("link must contain 'rel' as string");
            }
            if ("remotestorage" === $link['rel']) {
                // now we need at least href with storage URL and
                // properties containing the authURL and the version of the
                // spec being supported
                if (!array_key_exists("href", $link) || !is_string($link['href'])) {
                    throw new WebFingerException("link must contain href as string");
                }
                if (!array_key_exists("properties", $link) || !is_array($link['properties'])) {
                    throw new WebFingerException("link must contain properties as array");
                }
                $properties = $link['properties'];
                if (!array_key_exists("http://tools.ietf.org/html/rfc6749#section-4.2", $properties) || !is_string($properties['http://tools.ietf.org/html/rfc6749#section-4.2'])) {
                    throw new WebFingerException("property 'http://tools.ietf.org/html/rfc6749#section-4.2' missing or malformed");
                }
                if (!array_key_exists("http://remotestorage.io/spec/version", $properties) || !is_string($properties['http://remotestorage.io/spec/version'])) {
                    throw new WebFingerException("property 'http://remotestorage.io/spec/version' missing or malformed");
                }

            }
        }
    }
}
