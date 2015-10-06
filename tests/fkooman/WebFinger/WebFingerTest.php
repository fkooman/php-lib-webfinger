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
use GuzzleHttp\Client;
use GuzzleHttp\Subscriber\Mock;
use GuzzleHttp\Message\Response;

class WebFingerTest extends PHPUnit_Framework_TestCase
{
    public function testWebFingerResponse()
    {
        $client = new Client();
        $mock = new Mock([
            file_get_contents(__DIR__.'/data/webfinger-response.txt'),
        ]);
        $client->getEmitter()->attach($mock);
        $w = new WebFinger($client);
        $webFingerData = $w->finger('fkooman@localhost');
        $this->assertEquals('acct:fkooman@localhost', $webFingerData->getSubject());
    }
}
