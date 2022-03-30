<?php namespace Omnipay\GetNet\Message;

class AccessTokenRequest extends AbstractRequest
{
    protected $requestMethod = 'POST';
    protected $version = 2;
    protected $resource = 'token';

    public function getData()
    {
        $this->validate('client_id', 'client_secret');

        return [
            'scope'     => 'oob',
            'grant_type' => 'client_credentials'
        ];
    }

    public function sendData($data)
    {
        $method = $this->requestMethod;
        $url = $this->getEndpoint();
        $headers = [
            'Accept'        => 'application/json, text/plain, */*',
            'content-type'  => 'application/x-www-form-urlencoded',
            'Authorization' => 'Basic '.base64_encode($this->getClientId().':'.$this->getClientSecret()),
        ];

        $response = $this->httpClient->request(
            $method,
            $url,
            $headers,
            //$this->toJSON($data)
            http_build_query($data, '', '&')
        );

        if ($response->getStatusCode() != 200 && $response->getStatusCode() != 201 && $response->getStatusCode() != 400) {
            $array = [
                'error' => [
                    'code' => $response->getStatusCode(),
                    'message' => $response->getReasonPhrase()
                ]
            ];

            return $this->response = $this->createResponse($array);
        }

        $json = $response->getBody()->getContents();
        $array = @json_decode($json, true);

        return $this->response = $this->createResponse(@$array);
    }

    protected function getEndpoint()
    {
        return $this->getTestMode() ? $this->testEndpoint . '/auth/oauth/v2/token' : $this->liveEndpoint . '/auth/oauth/v2/token';
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
