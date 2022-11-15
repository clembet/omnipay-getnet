<?php

namespace Omnipay\GetNet\Message;

abstract class AbstractRequest extends \Omnipay\Common\Message\AbstractRequest
{
    protected $liveEndpoint = 'https://api.getnet.com.br';
    protected $testEndpoint = 'https://api-sandbox.getnet.com.br';
    protected $requestMethod = 'POST';
    protected $version = 1;
    protected $resource = '';

    public function getData()
    {
        $this->validate('seller_id', 'client_id', 'client_secret', 'authorization');
        return [];
    }

    public function sendData($data)
    {
        $this->validate('authorization');
        $method = $this->requestMethod;
        $url = $this->getEndpoint();

        $headers = [
            'Accept'        => 'application/json, text/plain, */*',
            'content-type'  => 'application/json',//application/x-www-form-urlencoded
            'Authorization' => $this->getAuthorization(),

        ];

        $response = $this->httpClient->request(
            $method,
            $url,
            $headers,
            $this->toJSON($data)
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

    public function __get($name)
    {
        return $this->getParameter($name);
    }
    
    public function getSellerId()
    {
        return $this->getParameter('seller_id');
    }

    public function setSellerId($value)
    {
        return $this->setParameter('seller_id', $value);
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

    public function setAccessToken($value)
    {
        return $this->setParameter('access_token', $value);
    }

    public function getAccessToken()
    {
        return $this->getParameter('access_token');
    }

    public function setAuthorization($value)
    {
        return $this->setParameter('authorization', $value);
    }

    public function getAuthorization()
    {
        return $this->getParameter('authorization');
    }

    public function setSessionId($value)// Caracteres permitidos [a-z], [A-Z], 0-9, _, - {tamanho: de 12 a 80 caracteres}
    {
        return $this->setParameter('session_id', $value);
    }

    public function getSessionId()
    {
        return $this->getParameter('session_id');
    }





    public function getOrderId()
    {
        return $this->getParameter('order_id');
    }

    public function setOrderId($value)
    {
        return $this->setParameter('order_id', $value);
    }

    

    public function getCustomer()
    {
        return $this->getParameter('customer');
    }

    public function setCustomer($value)
    {
        return $this->setParameter('customer', $value);
    }

    public function getCustomerId()
    {
        return $this->getParameter('customer_id');
    }

    public function setCustomerId($value)
    {
        return $this->setParameter('customer_id', $value);
    }    

    public function setInstallments($value)
    {
        return $this->setParameter('installments', $value);
    }
    public function getInstallments()
    {
        return $this->getParameter('installments');
    }

    public function setSoftDescriptor($value)
    {
        return $this->setParameter('soft_descriptor', $value);
    }
    public function getSoftDescriptor()
    {
        return $this->getParameter('soft_descriptor');
    }

    public function getPaymentType()
    {
        return $this->getParameter('paymentType');
    }

    public function setPaymentType($value)
    {
        $this->setParameter('paymentType', $value);
    }

    public function getDueDate()
    {
        $dueDate = $this->getParameter('dueDate');
        if($dueDate)
            return $dueDate;

        $time = localtime(time());
        $ano = $time[5]+1900;
        $mes = $time[4]+1+1;
        $dia = 1;// $time[3];
        if($mes>12)
        {
            $mes=1;
            ++$ano;
        }

        $dueDate = sprintf("%04d-%02d-%02d", $ano, $mes, $dia);
        $this->setDueDate($dueDate);

        return $dueDate;
    }

    public function setDueDate($value)
    {
        return $this->setParameter('dueDate', $value);
    }

    protected function decode($data)
    {
        return json_decode($data, true);
    }

    protected function getVersion()
    {
        return $this->version;
    }

    protected  function getResource()
    {
        return $this->resource;
    }

    protected function getMethod()
    {
        return $this->requestMethod;
    }

    public function toJSON($data, $options = 0)
    {
        if (version_compare(phpversion(), '5.4.0', '>=') === true) {
            return json_encode($data, $options | 64);
        }
        return str_replace('\\/', '/', json_encode($data, $options));
    }

    public function setShippingPrice($value)
    {
        return $this->setParameter('shipping_price', $value);
    }

    public function getShippingPrice()
    {
        return $this->getParameter('shipping_price');
    }

    public function getTransactionID()
    {
        return $this->getParameter('transactionId');
    }

    public function setTransactionID($value)
    {
        return $this->setParameter('transactionId', $value);
    }

    protected function createResponse($data)
    {
        $this->response = new Response($this, $data);
        $this->response->setTestMode($this->getTestMode());
        return $this->response;
    }

    protected function getEndpoint()
    {
        $version = $this->getVersion();
        $endPoint = ($this->getTestMode()?$this->testEndpoint:$this->liveEndpoint);
        return  "{$endPoint}/v{$version}/{$this->getResource()}";
    }

    public function getClientIp()
    {
        $ip = $this->getParameter('clientIp');
        if($ip)
            return $ip;

        $ip = "127.0.0.1";
        if(!empty($_SERVER['HTTP_CLIENT_IP'])){
            //ip from share internet
            $ip = @$_SERVER['HTTP_CLIENT_IP'];
        }elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
            //ip pass from proxy
            $ip = @$_SERVER['HTTP_X_FORWARDED_FOR'];
        }elseif(!empty($_SERVER['HTTP_X_FORWARDED'])){
            //ip pass from proxy
            $ip = @$_SERVER['HTTP_X_FORWARDED'];
        }elseif(!empty($_SERVER['HTTP_FORWARDED'])){
            //ip pass from proxy
            $ip = @$_SERVER['HTTP_FORWARDED'];
        }elseif(!empty($_SERVER['REMOTE_ADDR'])){
            $ip = @$_SERVER['REMOTE_ADDR'];
        }

        return $ip;
    }

    public function getItemData()
    {
        $data = [];
        $items = $this->getItems();

        if ($items) {
            foreach ($items as $n => $item) {
                $item_array = [];
                $item_array['id'] = $n+1;
                $item_array['title'] = $item->getName();
                $item_array['description'] = $item->getName();
                //$item_array['category_id'] = $item->getCategoryId();
                $item_array['quantity'] = (int)$item->getQuantity();
                //$item_array['currency_id'] = $this->getCurrency();
                $item_array['unit_price'] = (double)($this->formatCurrency($item->getPrice()));

                array_push($data, $item_array);
            }
        }

        return $data;
    }

    public function getDataCreditCard()
    {
        $this->validate('card', 'customer', 'customer_id', 'currency');

        //$payer = $this->getPayerData();
        $card = $this->getCard();
        $customer = $this->getCustomer();

        $data = [
            "seller_id" => $this->getSellerId(),// opcional
            "amount" => (int)($this->getAmount()*100.0),
            "currency" => $this->getCurrency(),
            "order" => [
                "order_id" => $this->getOrderId(),
                //"sales_tax" => 0, // valor dos impostos
                //"product_type" => "",//physical_goods, digital_content, digital_goods, digital_physical, cash_carry, gift_card, service
            ],
            "customer" => [
                "customer_id" => $this->getCustomerId(),
                "first_name" => $card->getFirstName(),
                "last_name" => $card->getLastName(),
                "name" => $card->getName(),
                "email" => $card->getEmail(),
                "phone_number" => $card->getPhone(),
                "document_type" => "CPF",
                "document_number" => $card->getHolderDocumentNumber(),
                "billing_address" => [
                    "street"=> $customer->getBillingAddress1(),
                    "number"=> $customer->getBillingNumber(),
                    "complement"=> $customer->getBillingAddress2(),
                    "district"=> $customer->getBillingDistrict(),
                    "city"=> $customer->getBillingCity(),
                    "state"=> $customer->getBillingState(),
                    "country"=> "Brasil",
                    "postal_code"=> $customer->getBillingPostcode()
                ],
            ],
            "shippings" => [
                [
                    "phone_number" => $card->getPhone(),
                    "shipping_amount" => (int)($this->getShippingPrice()*100.0),
                    "address" => [
                        "street"=> $customer->getShippingAddress1(),
                        "number"=> $customer->getShippingNumber(),
                        "complement"=> $customer->getShippingAddress2(),
                        "district"=> $customer->getShippingDistrict(),
                        "city"=> $customer->getShippingCity(),
                        "state"=> $customer->getShippingState(),
                        "country"=> "Brasil",
                        "postal_code"=> $customer->getShippingPostcode()
                    ],
                ]
            ],
            "credit"=> [
                "delayed"=> true, // true => faz a autorização, false => faz autorização + captura
                //'authenticated'=>'false', // não habilitar
                "save_card_data"=> false,
                //'pre_authorization'=>'false', // só habilitar o campo quando for fazer uma pré autorização
                "transaction_type"=> $this->getInstallments()>1?'INSTALL_NO_INTEREST':'FULL', // FULL, INSTALL_NO_INTEREST, INSTALL_WITH_INTEREST
                "number_installments"=> $this->getInstallments(),
                "soft_descriptor"=> $this->getSoftDescriptor(),
                //"dynamic_mcc": 1799, //Campo utilizado para sinalizar a transação com outro Merchant Category Code (Código da Categoria do Estabelecimento) diferente do cadastrado.
                "card"=> [
                  "number_token"=> $card->getNumberToken(),
                  //"bin"=> $card->getBin(),
                  "security_code"=> $card->getCvv(),
                  "expiration_month"=> sprintf("%02d", $card->getExpiryMonth()),
                  "expiration_year"=> substr(strval($card->getExpiryYear()), -2),
                  "cardholder_name"=> $card->getName()
                ]
            ],
        ];

        if(strlen($this->getSessionId())>3)
        {
            $data["device"] = [ // necessário para antifraude
                "ip_address" => $this->getClientIp(),
                "device_id" => $this->getSessionId(),
            ];
        }

        return $data;
    }

    public function getDataBoleto() //https://developers.getnet.com.br/api#tag/Pagamento%2Fpaths%2F~1v1~1payments~1boleto%2Fpost
    {
        $this->validate('customer','dueDate');
        //$payer = $this->getPayerData();
        $customer = $this->getCustomer();

        $data = [
                "seller_id" => $this->getSellerId(),// opcional
                "amount" => (int)($this->getAmount()*100.0),
                "currency" => $this->getCurrency(),
                "order" => [
                    "order_id" => $this->getOrderId(),
                    //"sales_tax" => 0, // valor dos impostos
                    //"product_type" => "",//physical_goods, digital_content, digital_goods, digital_physical, cash_carry, gift_card, service
                ],
                "boleto"=> [
                  "document_number"=> $customer->getDocumentNumber(),
                  "expiration_date"=> date('d/m/Y', strtotime($this->getDueDate())),
                  "instructions"=> "Não receber após o vencimento",
                  "provider"=> "santander"
                ],
                "customer"=> [
                  "first_name"=> $customer->getFirstName(),
                  "name"=> $customer->getName(),
                  "document_type"=> "CPF",
                  "document_number"=> $customer->getDocumentNumber(),
                  "billing_address"=> [
                    "street"=> $customer->getShippingAddress1(),
                    "number"=> $customer->getShippingNumber(),
                    "complement"=> $customer->getShippingAddress2(),
                    "district"=> $customer->getShippingDistrict(),
                    "city"=> $customer->getShippingCity(),
                    "state"=> $customer->getShippingState(),
                    "postal_code"=> $customer->getShippingPostcode()
                    ]
                ],
        ];

        return $data;
    }

    public function getDataPix() //https://developers.getnet.com.br/api#tag/PIX
    {
        $this->validate('amount', 'customer_id', 'currency');

        $data = [
            "amount"=> (int)($this->getAmount()*100.0),
            "currency"=> $this->getCurrency(),
            "order_id"=> $this->getOrderId(),
            "customer_id"=> $this->getCustomerId()
        ];

        return $data;
    }
}

?>
