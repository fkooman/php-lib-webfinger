<?php

/**
 * Copyright 2015 François Kooman <fkooman@tuxed.net>.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
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
                    __DIR__.'/data/fkooman@localhost'
                ),
                true
            )
        );
        $this->assertEquals('acct:fkooman@localhost', $w->getSubject());
        $this->assertEquals('https://localhost/php-remote-storage/api.php/fkooman', $w->getHref('remotestorage'));
        $this->assertEquals(
            'https://localhost/php-oauth-as/authorize.php?x_resource_owner_hint=fkooman',
            $w->getProperty('remotestorage', 'http://tools.ietf.org/html/rfc6749#section-4.2')
        );
        $this->assertEquals(array('remotestorage'), $w->getLinkRelations());
    }
}
