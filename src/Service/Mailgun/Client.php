<?php

namespace ConferenceTools\Sponsorship\Service\Mailgun;

use Zend\Http\Client as HttpClient;
use Zend\Http\Request;
use Zend\Stdlib\Parameters;

class Client
{
    const EVENTS_URI = 'https://api.mailgun.net/v3/%s/events';
    private $httpClient;
    private $domain;

    /**
     * Client constructor.
     * @param $httpClient
     * @param $domain
     * @param $secretKey
     */
    public function __construct(HttpClient $httpClient, $domain)
    {
        $this->httpClient = $httpClient;
        $this->domain = $domain;
    }

    public function fetchEmailMessages($since = '1496496708')
    {
        $messages = [];

        $responsePayload = $this->fetchEvents($since);

        if (isset($responsePayload['items'])) {
            foreach ($responsePayload['items'] as $event) {
                $messages[] = $this->fetchMessage($event['storage']['url']);
            }
        }

        return $messages;
    }

    /**
     * @param $since
     * @return array
     * @throws \Exception
     */
    private function fetchEvents($since): array
    {
        $request = new Request();
        $request->setQuery(new Parameters(['event' => 'stored', 'end' => $since]));
        $request->setUri(sprintf(self::EVENTS_URI, $this->domain));

        $response = $this->httpClient->send($request);
        if (!$response->isOk()) {
            throw new \Exception('Bad response (events) //@TODO add better error handling here');
        }

        //@TODO handle cases where this doesn't decode properly
        return json_decode($response->getBody(), \JSON_OBJECT_AS_ARRAY);
    }

    /**
     * @param $event
     * @throws \Exception
     */
    private function fetchMessage($from)
    {
        $request = new Request();
        $request->setUri($from);
        $response = $this->httpClient->send($request);

        if (!$response->isOk()) {
            throw new \Exception('Bad response (message) //@TODO add better error handling here');
        }

        //@TODO handle cases where this doesn't decode properly
        return json_decode($response->getBody(), \JSON_OBJECT_AS_ARRAY);
    }
}