<?php namespace Omnipay\GetNet\Message;

/**
 *  O Cancelamento é aplicavel a transações do mesmo dia sendo autorizadas ou aprovadas
 *  O Estono é aplicável para transações onde virou o dia, seguindo o processo do adquirente
 * <code>
 *   // Do a refund transaction on the gateway
 *   $transaction = $gateway->void(array(
 *       'transactionId'     => $transactionCode,
 *   ));
 *
 *   $response = $transaction->send();
 *   if ($response->isSuccessful()) {
 *   }
 * </code>
 */

class VoidRequest extends AbstractRequest   // está dando  erro para vendas com cartao parcelado, não permitindo estornar individualmente o pagamento
{
    //https://developers.getnet.com.br/api#tag/Pagamento%2Fpaths%2F~1v1~1payments~1credit~1%7Bpayment_id%7D~1cancel%2Fpost
    protected $resource = 'payments/credit';
    protected $requestMethod = 'POST';


    public function getData()
    {
        $this->validate('transactionId');
        //$data = parent::getData();
        $data = [];

        return $data;
    }

    public function sendData($data)
    {
        $this->validate('transactionId');

        $method = $this->requestMethod;
        $url = sprintf(
            "%s/%s/cancel",
            $this->getEndpoint(),
            $this->getTransactionID()
        );

        $headers = [
            'Accept'        => 'application/json, text/plain, */*',
            'content-type'  => 'application/json',//application/x-www-form-urlencoded
            'Authorization' => "Bearer ".$this->getAuthorization(),

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
