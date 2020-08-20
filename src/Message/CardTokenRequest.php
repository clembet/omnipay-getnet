<?php

namespace Omnipay\GetNet\Message;

class CardTokenRequest extends AbstractRequest
{
    protected $liveEndpoint = 'https://api.getnet.com.br/';
    protected $testEndpoint = 'https://api-sandbox.getnet.com.br';

    public function getData()
    {
        $this->validate('card_number');

        return [
            'card_number'     => $this->getCardNumber()
        ];
    }

    public function sendData($data)
    {
        $this->validate('authorization');

        $url = $this->getEndpoint();
        $headers = [
            'Accept'=> 'application/json, text/plain, */*',
            'content-type' => 'application/json',
            'authorization'     => $this->getAuthorization(),

        ];

        $response = $this->httpClient->request('POST',
            $url,
            $headers,
            json_encode($data)
        );

        $payload = $this->decode($response->getBody()->getContents());

        return $this->response = $this->createResponse(@$payload);
    }

    protected function getEndpoint()
    {
        return $this->getTestMode() ? $this->testEndpoint . '/v1/tokens/card' : $this->liveEndpoint . '/v1/tokens/card';
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

    public function getAuthorization()
    {
        return $this->getParameter('authorization');
    }

    public function setAuthorization($value)
    {
        return $this->setParameter('authorization', $value);
    }

    public function getCardNumber()
    {
        return $this->getParameter('card_number');
    }

    public function setCardNumber($value)
    {
        return $this->setParameter('card_number', $value);
    }

    protected function createResponse($data)
    {
        return $this->response = new CardTokenResponse($this, $data);
    }

    protected function decode($data)
    {
        return json_decode($data, true);
    }
}

?>
