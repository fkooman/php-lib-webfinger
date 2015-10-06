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

use fkooman\WebFinger\Exception\WebFingerException;
use GuzzleHttp\Client;
use GuzzleHttp\Response;
use GuzzleHttp\Exception\RequestException;
use InvalidArgumentException;

class WebFinger
{
    /** @var \GuzzleHttp\Client */
    private $client;

    /** @var array */
    private $options;

    public function __construct(Client $client = null)
    {
        if (null === $client) {
            $client = new Client();
        }
        $this->client = $client;
        $this->options = array(
            'verify' => true,
            'ignore_media_type' => false,
        );
    }

    public function setOption($key, $value)
    {
        if (!array_key_exists($key, $this->options)) {
            throw new InvalidArgumentException('unsupported option');
        }
        $this->options[$key] = $value;
    }

    public function finger($resource)
    {
        // verify the resource - we cannot use filter_var here as it does not
        // accept `localhost` as a valid domain...
        if (false === strpos($resource, '@')) {
            throw new WebFingerException('resource must be formatted as an email address');
        }
        $domainName = explode('@', $resource)[1];
        $webFingerUri = sprintf('https://%s/.well-known/webfinger?resource=acct:%s', $domainName, $resource);

        try {
            $response = $this->client->get(
                $webFingerUri,
                array(
                    'verify' => $this->options['verify'],
                    'protocols' => array('https')
                )
            );

            if (!$this->options['ignore_media_type']) {
                if ('application/jrd+json' !== $response->getHeader('Content-Type')) {
                    throw new WebFingerException(
                        sprintf(
                            'invalid media type, expected "application/jrd+json", got "%s"',
                            $response->getHeader('Content-Type')
                        )
                    );
                }
            }
            if ('*' !== $response->getHeader('Access-Control-Allow-Origin')) {
                throw new WebFingerException(
                    'CORS header "Access-Control-Allow-Origin" missing or invalid'
                );
            }

            return new WebFingerData($response->json());
        } catch (RequestException $e) {
            // a 404 is a normal response when the resource does not exist, so
            // we wrap that here in a WebFingerException, so any other
            // Exceptions came from Guzzle and can be considered fatal...
            if (404 === $e->getCode()) {
                throw new WebFingerException('resource not found');
            }
            throw $e;
        }
    }
}
