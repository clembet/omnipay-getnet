<?php namespace Omnipay\GetNet\Message;

use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RedirectResponseInterface;

/**
 * Pagarme Response
 *
 * This is the response class for all Pagarme requests.
 *
 * @see \Omnipay\Pagarme\Gateway
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
    public function getTransactionReference()
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
}