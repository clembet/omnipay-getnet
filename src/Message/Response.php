<?php namespace Omnipay\GetNet\Message;

use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RedirectResponseInterface;

/**
 * Response
 *
 * This is the response class for all requests.
 *
 * @see \Omnipay\GetNet\Gateway
 */
class Response extends AbstractResponse
{
    /**
     * Is the transaction successful?
     *
     * @return bool
     */
    public function isSuccessful()
    {
        $status_code = @$this->data['status_code'];
        if (($status_code==200)||($status_code==201)||($status_code==202))
            return true;

        if (isset($this->data['payment_id']) && isset($this->data['credit']['authorization_code']))
            return true;

        return false;
    }

    /**
     * Get the transaction reference.
     *
     * @return string|null
     */
    public function getTransactionID()
    {
        if ($this->isSuccessful()) {
            if(isset($this->data['payment_id']))
                return @$this->data['payment_id']; //Código de identificação do pagamento.
            // tem ['credit']['transaction_id'] //Código de transação.
        }

        return null;
    }

    public function getTransactionAuthorizationCode()
    {
        if ($this->isSuccessful()) {
            if(isset($this->data['credit']['authorization_code']))
                return $this->data['credit']['authorization_code'];//Código interno da transação.
        }

        return null;
    }

    /**
     * Get the error message from the response.
     *
     * Returns null if the request was successful.
     *
     * @return string|null
     */
    public function getMessage()
    {
        if (!$this->isSuccessful()) {

            if(isset($this->data['message']))
                return $this->data['message'].' : '.@json_encode(@$this->data['details']);
        }

        return null;
    }

    public function getStatus() // https://developers.getnet.com.br/api#tag/Pagamento%2Fpaths%2F~1v1~1payments~1credit%2Fpost   https://developers.getnet.com.br/api#tag/Notificacoes-1.1
    {
        $status = null;
        if(isset($this->data['status']))
            $status = @$this->data['status'];

        return $status;
    }

    public function isPaid()
    {
        $status = $this->getStatus();
        return (strcmp($status, "APPROVED")==0||strcmp($status, "CONFIRMED")==0||strcmp($status, "PAID")==0);
    }

    public function isAuthorized()
    {
        $status = $this->getStatus();
        return strcmp($status, "AUTHORIZED")==0;
    }

    public function isPending()
    {
        $status = $this->getStatus();
        return (strcmp($status, "PENDING")==0||strcmp($status, "WAITING")==0);
    }

    public function isVoided()
    {
        $status = $this->getStatus();
        return strcmp($status, "CANCELED")==0;
    }

    public function getBoleto()//https://www.mercadopago.com.br/developers/pt/guides/online-payments/checkout-api/other-payment-ways
    {
        $data = $this->getData();
        $boleto = array();
        $boleto['boleto_url'] = @$data['transaction_details']['external_resource_url'];
        $boleto['boleto_url_pdf'] = @$data['transaction_details']['external_resource_url'];
        $boleto['boleto_barcode'] = "";//@$data['transaction_details']['DigitableLine'];//TODO:
        $boleto['boleto_expiration_date'] = NULL;//@$data['transaction_details']['ExpirationDate'];//TODO:
        $boleto['boleto_valor'] = (@$data['transaction_details']['total_paid_amount']*1.0);
        $boleto['boleto_transaction_id'] = @$data['id'];
        //@$this->setTransactionReference(@$data['transaction_id']);

        return $boleto;
    }

    public function getPix() // https://www.mercadopago.com.br/developers/pt/guides/online-payments/checkout-api/receiving-payment-by-pix
    {
        $data = $this->getData();
        $pix = array();
        $pix['pix_qrcodebase64image'] = @$data['point_of_interaction']['transaction_data']['qr_code_base64'];
        $pix['pix_qrcodestring'] = @$data['point_of_interaction']['transaction_data']['qr_code'];
        $pix['pix_valor'] = (@$data['transaction_details']['total_paid_amount']*1.0);
        $pix['pix_transaction_id'] = @$data['id'];

        return $pix;
    }

    //https://developers.getnet.com.br/api#tag/Pagamento%2Fpaths%2F~1v1~1payments~1credit%2Fpost

}