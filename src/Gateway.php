<?php

namespace Omnipay\GetNet;

use Omnipay\Common\AbstractGateway;
use Omnipay\Common\ItemBag;

/**
 * @method \Omnipay\Common\Message\RequestInterface completeAuthorize(array $options = array())
 * @method \Omnipay\Common\Message\RequestInterface completePurchase(array $options = array())
 * @method \Omnipay\Common\Message\RequestInterface refund(array $options = array())
 * @method \Omnipay\Common\Message\RequestInterface createCard(array $options = array())
 * @method \Omnipay\Common\Message\RequestInterface updateCard(array $options = array())
 * @method \Omnipay\Common\Message\RequestInterface deleteCard(array $options = array())
 */

class Gateway extends AbstractGateway
{
    public function getName()
    {
        return 'GetNet';
    }

    public function getDefaultParameters()
    {
        return [
            'seller_id' => '',
            'client_id' => '',
            'client_secret' => '',
            'testMode' => false,
        ];
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

    
/*
    public function setExternalReference($value)
    {
        return $this->setParameter('external_reference', $value);
    }

    public function getExternalReference()
    {
        return $this->getParameter('external_reference');
    }
*/

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

    
        
    public function requestAccessToken(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\GetNet\Message\AccessTokenRequest', $parameters);
    }

    public function requestCardToken(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\GetNet\Message\CardTokenRequest', $parameters);
    }



    /**
     * Authorize Request
     *
     * An Authorize request is similar to a purchase request but the
     * charge issues an authorization (or pre-authorization), and no money
     * is transferred.  The transaction will need to be captured later
     * in order to effect payment. Uncaptured charges expire in 5 days.
     *
     * Either a card object or card_id is required by default. Otherwise,
     * you must provide a card_hash, like the ones returned by Braspag
     *
     * Braspag gateway supports only two types of "payment_method":
     *
     * * credit_card
     *
     * Optionally, you can provide the customer details to use the antifraude
     * feature. These details is passed using the following attributes available
     * on credit card object:
     *
     * * firstName
     * * lastName
     * * address1 (must be in the format "street, street_number and neighborhood")
     * * address2 (used to specify the optional parameter "street_complementary")
     * * postcode
     * * phone (must be in the format "DDD PhoneNumber" e.g. "19 98888 5555")
     *
     * @param array $parameters
     * @return \Omnipay\GetNet\Message\AuthorizeRequest
     */
    public function authorize(array $parameters = [])//ok
    {
        return $this->createRequest('\Omnipay\GetNet\Message\AuthorizeRequest', $parameters);
    }

    /**
     * Capture Request
     *
     * Use this request to capture and process a previously created authorization.
     *
     * @param array $parameters
     * @return \Omnipay\GetNet\Message\CaptureRequest
     */
    public function capture(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\GetNet\Message\CaptureRequest', $parameters);
    }

    /**
     * Purchase request.
     *
     * To charge a credit card  you create a new transaction
     * object. If your MerchantID is in test mode, the supplied card won't actually
     * be charged, though everything else will occur as if in live mode.
     *
     * Either a card object or card_id is required by default. Otherwise,
     * you must provide a card_hash, like the ones returned by Braspag
     *
     * Braspag gateway supports only one type of "payment_method":
     *
     * * credit_card
     *
     *
     * Optionally, you can provide the customer details to use the antifraude
     * feature. These details is passed using the following attributes available
     * on credit card object:
     *
     * * firstName
     * * lastName
     * * address1 (must be in the format "street, street_number and neighborhood")
     * * address2 (used to specify the optional parameter "street_complementary")
     * * postcode
     * * phone (must be in the format "DDD PhoneNumber" e.g. "19 98888 5555")
     *
     * @param array $parameters
     * @return \Omnipay\GetNet\Message\PurchaseRequest
     */
    public function purchase(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\GetNet\Message\PurchaseRequest', $parameters);
    }

    /**
     * Void Transaction Request
     *
     *
     *
     * @param array $parameters
     * @return \Omnipay\GetNet\Message\VoidRequest
     */
    public function void(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\GetNet\Message\VoidRequest', $parameters);
    }

    public function fetchTransaction(array $parameters = [])
    {
        return $this->createRequest('\Omnipay\GetNet\Message\FetchTransactionRequest', $parameters);
    }

    public function acceptNotification(array $parameters = [])
    {
        return $this->createRequest('\Omnipay\GetNet\Message\NotificationRequest', $parameters);
    }

    public function parseResponse($data)
    {
        $request = $this->createRequest('\Omnipay\GetNet\Message\PurchaseRequest', []);
        return new \Omnipay\GetNet\Message\Response($request, (array)$data);
    }

    public function getAntifraudeScriptUrl()
    {
        $session_id = $this->getSessionId();
        $ambiente = "1snn5n9w";// ambiente de teste
        if(!$this->getTestMode())
            $ambiente = "k8vif92e";// ambiente de produção
        return "https://h.online-metrix.net/fp/tags.js?org_id=$ambiente&session_id=$session_id";
    }
}

?>
