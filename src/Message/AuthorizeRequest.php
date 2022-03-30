<?php namespace Omnipay\GetNet\Message;

use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Common\ItemBag;

class AuthorizeRequest extends AbstractRequest
{
    protected $resource = 'payments/credit';
    /**
     * Get the raw data array for this message. The format of this varies from gateway to
     * gateway, but will usually be either an associative array, or a SimpleXMLElement.
     *
     * @return mixed
     */

    public function getData()
    {
        $this->validate('customer', 'paymentType');

        $data = [];
        switch(strtolower($this->getPaymentType()))
        {
            case 'creditcard':
                $data = $this->getDataCreditCard();
                $this->resource = "payments/credit";
                break;

            case 'boleto':
                $data = $this->getDataBoleto();
                $this->resource = "payments/boleto";
                break;

            case 'pix':
                $data = $this->getDataPix();
                $this->resource = "payments/qrcode/pix";
                break;

            default:
                $data = $this->getDataCreditCard();
                $this->resource = "payments/credit";
        }

        return $data;
    }


}
