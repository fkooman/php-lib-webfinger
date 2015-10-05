<?php

namespace fkooman\WebFinger;

use fkooman\WebFinger\Exception\WebFingerException;
use GuzzleHttp\Client;
use GuzzleHttp\Response;
use GuzzleHttp\Exception\RequestException;

class WebFinger
{
    /** @var array */
    private $options;

    /** @var \GuzzleHttp\Client */
    private $client;

    public function __construct(array $options = array(), Client $client = null)
    {
        $this->options = $options;
        if (null === $client) {
            $client = new Client(
                array(
                    'protocols' => array('https'),
                )
            );
        }
        $this->client = $client;
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
                    'verify' => $this->getOption('verify', true),
                )
            );

            if (!$this->getOption('ignore_media_type', false)) {
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
                    'Access-Control-Allow-Origin header missing or invalid'
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

    private function getOption($key, $default)
    {
        return array_key_exists($key, $this->options) ? $this->options[$key] : $default;
    }
}
