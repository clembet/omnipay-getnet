<?php

namespace Omnipay\GetNet\Message;

use Omnipay\Common\Message\AbstractResponse;

class CardTokenResponse extends AbstractResponse
{
    public function isSuccessful()
    {
        return isset($this->data['number_token']) && (strlen(@$this->data['number_token'])>3);
    }

    public function getNumberToken()
    {
        return @$this->data['number_token'];
    }
}

?>
