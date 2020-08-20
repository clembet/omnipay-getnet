<?php

namespace Omnipay\GetNet;

use Omnipay\Common\AbstractGateway;
use Omnipay\Common\ItemBag;

class Gateway extends AbstractGateway
{
    public function getName()
    {
        return 'GetNet';
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

    public function setExternalReference($value)
    {
        return $this->setParameter('external_reference', $value);
    }

    public function getExternalReference()
    {
        return $this->getParameter('external_reference');
    }

    public function setAuthorization($value)
    {
        return $this->setParameter('authorization', $value);
    }

    public function getAuthorization()
    {
        return $this->getParameter('authorization');
    }

    public function setDeviceId($value)
    {
        return $this->setParameter('device_id', $value);
    }

    public function getDeviceId()
    {
        return $this->getParameter('device_id');
    }

    public function purchase(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\GetNet\Message\PurchaseRequest', $parameters);
    }

    public function requestAccessToken(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\GetNet\Message\AccessTokenRequest', $parameters);
    }

    public function requestCardToken(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\GetNet\Message\CardTokenRequest', $parameters);
    }

    public function getAntifraudeScriptUrl()
    {
        $device_id = $this->getDeviceId();
        $ambiente = "1snn5n9w";// ambiente de teste
        if(!$this->getTestMode())
            $ambiente = "k8vif92e";// ambiente de produção
        return "https://h.online-metrix.net/fp/tags.js?org_id=$ambiente&session_id=$device_id";
    }
}

?>
