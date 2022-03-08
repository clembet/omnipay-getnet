<?php

namespace Omnipay\GetNet\Message;

abstract class AbstractRequest extends \Omnipay\Common\Message\AbstractRequest
{
    protected $liveEndpoint = 'https://api.getnet.com.br/';
    protected $testEndpoint = 'https://api-sandbox.getnet.com.br';

    public function getData()
    {
        $data = $this->getExternalReference();
        return $data;
    }

    public function sendData($data)
    {
        $url = $this->getEndpoint();
        $headers = [
            'Accept'=> 'application/json, text/plain, */*',
            'content-type' => 'application/json',
            'authorization'     => $this->getAuthorization(),

        ];
        $response = $this->httpClient->request(
            'POST',
            $url,
            $headers,
            $this->toJSON($data)
        );

        $payload = $this->decode($response->getBody());

        return $this->response = $this->createResponse(@$payload);
    }

    public function setExternalReference($value)
    {
        return $this->setParameter('external_reference', $value);
    }

    public function getExternalReference()
    {
        return $this->getParameter('external_reference');
    }

    public function getSellerId()
    {
        return $this->getParameter('seller_id');
    }

    public function setSellerId($value)
    {
        return $this->setParameter('seller_id', $value);
    }

    public function getOrderId()
    {
        return $this->getParameter('order_id');
    }

    public function setOrderId($value)
    {
        return $this->setParameter('order_id', $value);
    }

    public function getDeviceId()
    {
        return $this->getParameter('device_id');
    }

    public function setDeviceId($value)
    {
        return $this->setParameter('device_id', $value);
    }

    public function getAuthorization()
    {
        return $this->getParameter('authorization');
    }

    public function setAuthorization($value)
    {
        return $this->setParameter('authorization', $value);
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

    protected function getEndpoint()
    {
        return $this->getTestMode() ? $this->testEndpoint : $this->liveEndpoint;
    }

    protected function decode($data)
    {
        return json_decode($data, true);
    }

    public function toJSON($data, $options = 0)
    {
        if (version_compare(phpversion(), '5.4.0', '>=') === true) {
            return json_encode($data, $options | 64);
        }
        return str_replace('\\/', '/', json_encode($data, $options));
    }

    public function getIpAddress()
    {
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
}

?>
