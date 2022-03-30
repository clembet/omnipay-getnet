<?php namespace Omnipay\GetNet\Message;

class CardTokenRequest extends AbstractRequest
{
    protected $requestMethod = 'POST';
    protected $version = 1;
    protected $resource = 'tokens/card';

    public function getData()
    {
        $this->validate('card_number');

        return [
            'card_number'     => $this->getCardNumber()
        ];
    }

    public function getCardNumber()
    {
        return $this->getParameter('card_number');
    }

    public function setCardNumber($value)
    {
        return $this->setParameter('card_number', $value);
    }

    protected function createResponse($data)
    {
        return $this->response = new CardTokenResponse($this, $data);
    }
}

?>
