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

    /** @var GuzzleHttp\Client */
    private $client;

    public function __construct(array $options = [])
    {
        $this->options = $options;
        $this->client = new Client();
    }

    public function discover($resource)
    {
        return $this->request($resource);
    }

    private function request($resource)
    {
        if (false === strpos($resource, "@")) {
            throw new WebFingerException("resource must be formatted as an email address");
        }
        $domainName = explode("@", $resource)[1];

        $webFingerUri = sprintf("https://%s/.well-known/webfinger?resource=acct:%s", $domainName, $resource);

        try {
            $response = $this->client->get(
                $webFingerUri,
                array(
                    "verify" => $this->getOption('verify', true)
                )
            );

            if (!$this->getOption('ignore_media_type', false)) {
                if ("application/jrd+json" !== $response->getHeader("Content-Type")) {
                    throw new WebFingerException("invalid media type");
                }
            }
            if ("*" !== $response->getHeader("Access-Control-Allow-Origin")) {
                $this->validateLog[] = "cors header access-control-allow-origin missing or invalid";
            }

            $webFingerData = $response->json();

            $this->validateData($webFingerData);

            return $webFingerData;
        } catch (RequestException $e) {
            if (404 === $e->getCode()) {
                throw new WebFingerException("resource not found");
            }
            throw $e;
        }
    }

    protected function validateData(array $webFingerData)
    {
    }

    private function getOption($key, $default)
    {
        return array_key_exists($key, $this->options) ? $this->options[$key] : $default;
    }
}
