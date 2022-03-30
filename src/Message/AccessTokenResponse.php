<?php namespace Omnipay\GetNet\Message;

use Omnipay\Common\Message\AbstractResponse;

class AccessTokenResponse extends AbstractResponse
{
    public function isSuccessful()
    {
        return isset($this->data['access_token']) && (strlen(@$this->data['access_token'])>3);
    }

    public function getAccessToken()
    {
        return @$this->data['access_token'];
    }

    public function getTokenType()
    {
        return @$this->data['token_type'];
    }

}

?>
