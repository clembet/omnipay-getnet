<?php

namespace Omnipay\GetNet\Message;

class AccessTokenRequest extends AbstractRequest
{
    protected $liveEndpoint = 'https://api.getnet.com.br/';
    protected $testEndpoint = 'https://api-sandbox.getnet.com.br';

    public function getData()
    {
        return [
            'scope'     => 'oob',
            'grant_type' => 'client_credentials'
        ];
    }

    public function sendData($data)
    {
        $url = $this->getEndpoint();
        $headers = [
            'authorization'     => 'Basic '.base64_encode($this->getClientId().':'.$this->getClientSecret()),
            'content-type' => 'application/x-www-form-urlencoded',
        ];

        $response = $this->httpClient->request('POST',
            $url,
            $headers,
            http_build_query($data, '', '&')
        );

        $payload = $this->decode($response->getBody()->getContents());

        return $this->response = $this->createResponse(@$payload);
    }

    protected function getEndpoint()
    {
        return $this->getTestMode() ? $this->testEndpoint . '/auth/oauth/v2/token' : $this->liveEndpoint . '/auth/oauth/v2/token';
    }

    public function getClientId()
    {
        return $this->getParameter('client_id');
    }

    public function setClientId($value)
    {
        return $this->setParameter('client_id', $value);
    }

    public function getClientSecret()
    {
        return $this->getParameter('client_secret');
    }

    public function setClientSecret($value)
    {
        return $this->setParameter('client_secret', $value);
    }

    protected function createResponse($data)
    {
        return $this->response = new AccessTokenResponse($this, $data);
    }

    protected function decode($data)
    {
        return json_decode($data, true);
    }
}

?>
