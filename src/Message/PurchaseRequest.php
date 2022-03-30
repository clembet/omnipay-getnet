<?php namespace Omnipay\GetNet\Message;

class PurchaseRequest extends AuthorizeRequest
{
    public function getData()
    {
        $data = parent::getData();
        if(strcmp(strtolower($this->getPaymentType()), "creditcard")==0)
            $data["credit"]["delayed"] = false; // quando delayed=false já faz a autorização e captura ao mesmo tempo

        return $data;
    }
}

?>
