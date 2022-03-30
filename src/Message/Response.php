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
    protected $liveEndpoint = 'https://api.getnet.com.br';
    protected $testEndpoint = 'https://api-sandbox.getnet.com.br';
    protected $isTestMode = false;
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

        if (isset($this->data['payment_id']) && isset($this->data['status'])  ||
            isset($this->data['payment_response']['payment_id']))
            return true;

        return false;
    }

    public function getTestMode()
    {
        return $this->isTestMode;
    }

    public function setTestMode($val)
    {
        $this->isTestMode = $val;
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

    public function getBoletoID()
    {
        if ($this->isSuccessful()) {
            if(isset($this->data['boleto']['boleto_id']))
                return @$this->data['boleto']['boleto_id']; //Código de identificação do pagamento.
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
            return @$this->data['status'];

        if(isset($this->data['payment_response']['status']))
            return @$this->data['payment_response']['status'];

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

    public function getBoleto() // https://developers.getnet.com.br/api#tag/Pagamento%2Fpaths%2F~1v1~1payments~1boleto%2Fpost
    {
        $data = $this->getData();
        $boleto = array();
        $payment_id = @$data['payment_id'];
        $endpoint = $this->getTestMode()?$this->testEndpoint:$this->liveEndpoint;
        $boleto['boleto_url'] = "$endpoint/v1/payments/boleto/$payment_id/html";//@$data['boleto']['links'][0]['href']; //'https://api-sandbox.getnet.com.br/v1/payments/boleto/{payment_id}/html'
        $boleto['boleto_url_pdf'] = "$endpoint/v1/payments/boleto/$payment_id/pdf";//@$data['boleto']['links'][0]['href'];  //'https://api-sandbox.getnet.com.br/v1/payments/boleto/{payment_id}/pdf'
        $boleto['boleto_barcode'] = @$data['boleto']['typeful_line'];
        $boleto['boleto_expiration_date'] = @$data['boleto']['expiration_date'];
        $boleto['boleto_valor'] = (@$data['amount']*1.0)/100.0;
        $boleto['boleto_transaction_id'] = @$data['boleto']['boleto_id'];//@$data['payment_id']
        //@$this->setTransactionReference(@$data['transaction_id']);

        return $boleto;
    }

    public function getPix()
    {
        $data = $this->getData();
        $pix = array();
        $pix['pix_qrcodebase64image'] = $this->createPixImg(@$data['additional_data']['qr_code']);
        $pix['pix_qrcodestring'] = @$data['additional_data']['qr_code'];
        $pix['pix_valor'] = NULL;//(@$data['amount']*1.0)/100.0;
        $pix['pix_transaction_id'] = @$data['payment_id'];

        return $pix;
    }

    public function createPixImg($pix)
    {
        include_once(dirname(__FILE__)."/phpqrcode.php");
        ob_start();
            \QRCode::png($pix, null,'M',5); //https://github.com/renatomb/php_qrcode_pix
            $imageString = base64_encode( ob_get_contents() );
        ob_end_clean();

        $base64 = 'data:image/png;base64,' . $imageString;
        return $base64;
    }

}