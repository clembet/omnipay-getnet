<?php namespace Omnipay\GetNet\Message;

class CaptureRequest extends AbstractRequest
{
    //https://developers.getnet.com.br/api#tag/Pagamento%2Fpaths%2F~1v1~1payments~1credit~1%7Bpayment_id%7D~1confirm%2Fpost
    protected $resource = 'payments/credit';
    protected $requestMethod = 'POST';


    public function getData()
    {
        $this->validate('transactionId', 'amount');
        //$data = parent::getData();

        $data = [
            "amount"=> (int)($this->getAmount()*100.0)
        ];

        return $data;
    }

    public function sendData($data)
    {
        $this->validate('transactionId', 'amount');

        $method = $this->requestMethod;
        $url = sprintf(
            "%s/%s/confirm",
            $this->getEndpoint(),
            $this->getTransactionID()
        );

        $headers = [
            'Accept'        => 'application/json, text/plain, */*',
            'content-type'  => 'application/json',//application/x-www-form-urlencoded
            'Authorization' => $this->getAuthorization(),

        ];

        //print_r([$method, $url, $headers, json_encode($data)]);exit();
        $response = $this->httpClient->request(
            $method,
            $url,
            $headers,
            $this->toJSON($data)
            //http_build_query($data, '', '&')
        );
        //print_r($response);
        //print_r($data);

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
        //print_r($array);

        return $this->response = $this->createResponse(@$array);
    }
}
