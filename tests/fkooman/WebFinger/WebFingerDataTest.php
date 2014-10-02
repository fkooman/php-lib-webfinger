<?php

namespace fkooman\WebFinger;

use PHPUnit_Framework_TestCase;

class WebFingerDataTest extends PHPUnit_Framework_TestCase
{
    public function testEmptyWebFingerData()
    {
        $w = new WebFingerData(array());
        $this->assertNull($w->getSubject());
        $this->assertNull($w->getProperty('remotestorage', 'http://tools.ietf.org/html/rfc6749#section-4.2'));
        $this->assertNull($w->getHref('remotestorage'));
    }

    public function testWebFingerDataOne()
    {
        $w = new WebFingerData(
            json_decode(
                file_get_contents(
                    dirname(dirname(__DIR__)) . "/data/fkooman@localhost"
                ),
                true
            )
        );
        $this->assertEquals("acct:fkooman@localhost", $w->getSubject());
        $this->assertEquals("https://localhost/php-remote-storage/api.php/fkooman", $w->getHref("remotestorage"));
        $this->assertEquals(
            "https://localhost/php-oauth-as/authorize.php?x_resource_owner_hint=fkooman",
            $w->getProperty("remotestorage", "http://tools.ietf.org/html/rfc6749#section-4.2")
        );
        $this->assertEquals(array("remotestorage"), $w->getLinkRelations());
    }
}
