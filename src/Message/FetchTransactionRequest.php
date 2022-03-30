<?php namespace Omnipay\GetNet\Message;

class FetchTransactionRequest extends AbstractRequest
{
    //https://developers.getnet.com.br/api#tag/QRCode%2Fpaths%2F~1v1~1payments~1qrcode%2Fpost
    //protected $resource = 'payments/credit';
    protected $resource = 'payments/qrcode';
    protected $requestMethod = 'GET';

    /**
     * Get the raw data array for this message. The format of this varies from gateway to
     * gateway, but will usually be either an associative array, or a SimpleXMLElement.
     *
     * @return mixed
     */
    public function getData()
    {
        return parent::getData();
    }

    public function sendData($data)
    {
        $this->validate('transactionId');

        $method = $this->requestMethod;
        $url = sprintf(
            "%s/%s",
            $this->getEndpoint(),
            $this->getTransactionID()
        );

        $headers = [
            'Accept'        => 'application/json, text/plain, */*',
            'content-type'  => 'application/json',//application/x-www-form-urlencoded
            'Authorization' => $this->getAuthorization(),
            //'seller_id'     => $this->getSellerId(),

        ];

        $response = $this->httpClient->request(
            $method,
            $url,
            $headers
            //$this->toJSON($data)
            //http_build_query($data, '', '&')
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

}
