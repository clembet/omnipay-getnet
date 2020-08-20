<?php

namespace Omnipay\GetNet;

use Omnipay\Tests\GatewayTestCase;

class GatewayTest extends GatewayTestCase
{
    public function setUp()
    {
        $this->gateway = new Gateway($this->getHttpClient(), $this->getHttpRequest());
        $this->gateway->setSellerId("<sellerID>");
        $this->gateway->setClientId("<clientID>");
        $this->gateway->setClientSecret("<clientSecret>");
        $this->gateway->setTestMode(true);
        $this->gateway->items = [
            [
                'title' => 'PurchaseTest',
                'quantity' => 1,
                'category_id' => 'tickets',
                'currency_id' => 'BRL',
                'unit_price' => 10.0
            ]
        ];
    }

    public function testPurchase()
    {
        $responseToken = $this->gateway->requestAccessToken()->send();
        $access_token = $responseToken->getAccessToken();
        $token_type = $responseToken->getTokenType();
        $this->gateway->setAuthorization($token_type.' '.$access_token);
        $this->assertInstanceOf('\Omnipay\GetNet\Message\AccessTokenResponse', $responseToken);
        $this->assertTrue($this->gateway->getAccessToken() != null);

        $response = $this->gateway->purchase($this->items)->send();
        $data = $response->getData();
        $this->assertInstanceOf('\Omnipay\GetNet\Message\Response', $response);
        $this->assertTrue($data['payment_id'] != null);
    }
}

?>
