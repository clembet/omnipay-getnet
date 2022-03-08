<?php

namespace Omnipay\GetNet\Message;

class PurchaseRequest extends AbstractRequest
{

    public function getItemData()
    {
        $data = [];
        $items = $this->getItems();

        if ($items) {
            foreach ($items as $n => $item) {

                $item_array = [];
                $item_array['title'] = $item->getName();
                $item_array['description'] = $item->getDescription();
//                $item_array['category_id'] = $item->getCategoryId();
                $item_array['quantity'] = (int)$item->getQuantity();
                $item_array['currency_id'] = $this->getCurrency();
                $item_array['unit_price'] = (double)($this->formatCurrency($item->getPrice()));

                array_push($data, $item_array);
            }
        }

        return $data;
    }

    public function getData()
    {
        $this->validate('authorization');
        $this->validate('seller_id');
        $this->validate('order_id');
        $this->validate('device_id');
        $this->validate('customer_id');

        $data = array();
        $card = $this->getCard();

        //https://developers.getnet.com.br/api#tag/Pagamento%2Fpaths%2F~1v1~1payments~1credit%2Fpost
        $purchaseObject = [
            'seller_id'         => $this->getSellerId(),
            'amount'            => $this->getAmountInteger(),
            'currency'          => $this->getCurrency(),
            'order'             => [
                'order_id'=>strval($this->getOrderId()),
                'sales_tax'=>'0',
            ],
            'customer'          => [
                'customer_id'   => ($this->getCustomerId()),
                'first_name'    => $card->getFirstName(),
                'last_name'     => $card->getLastName(),
                'name'          => $card->getName(),
                'email'         => $card->getEmail(),
                'document_type' => 'CPF',
                'document_number'=>$card->getHolderDocumentNumber(),
                'phone_number'=>$card->getPhone(),
                'billing_address'=>[
                    'street'=>$card->getBillingAddress1(),
                    'number'=>$card->getBillingNumber(),
                    'complement'=>$card->getBillingAddress2(),
                    'district'=>$card->getBillingDistrict(),
                    'city'=>$card->getBillingCity(),
                    'state'=>$card->getBillingState(),
                    'country'=>$card->getBillingCountry(),
                    'postal_code'=>$card->getBillingPostcode()
                ]
            ],
            'device' => [
                'ip_address' =>$this->getIpAddress(),
                'device_id'=>$this->getDeviceId()
            ],
            /*'shippings' => [
                'first_name'    => $card->getFirstName(),
                'name'          => $card->getName(),
                'email'         => $card->getEmail(),
                'phone_number'=>$card->getPhone(),
                //'shipping_amount'=>(int)round($card->getShippingAmount()*100.0, 0),
                'address'   =>[
                    'street'=>$card->getShippingAddress1(),
                    'number'=>$card->getShippingNumber(),
                    'complement'=>$card->getShippingAddress2(),
                    'district'=>$card->getShippingDistrict(),
                    'city'=>$card->getShippingCity(),
                    'state'=>$card->getShippingState(),
                    'country'=>$card->getShippingCountry(),
                    'postal_code'=>$card->getShippingPostcode()
                ]
            ],*/
            'credit'          => [
                'delayed' => 'false',
                //'authenticated'=>'false', // não habilitar
                'save_card_data' => 'false',
                //'pre_authorization'=>'false', // só habilitar o campo quando for fazer uma pré autorização
                'transaction_type' =>$this->getInstallments()>1?'INSTALL_NO_INTEREST':'FULL',
                'number_installments' => $this->getInstallments(),
                'soft_descriptor'=>$this->getSoftDescriptor(),
                //"dynamic_mcc": 1799, //Campo utilizado para sinalizar a transação com outro Merchant Category Code (Código da Categoria do Estabelecimento) diferente do cadastrado.
                'card' => [
                    'number_token' => $card->getNumberToken(),
                    //'bin' =>'123412',
                    'cardholder_name' => $card->getName(),
                    'security_code' => $card->getCvv(),
                    'brand'         => $card->getBrand(),
                    'expiration_month'=> sprintf("%02d", $card->getExpiryMonth()),
                    'expiration_year'=> substr(strval($card->getExpiryYear()), -2),
                ]
            ],
        ];

        return $purchaseObject;

    }

    public function getInstallments()
    {
        return $this->getParameter('installments');
    }

    public function setInstallments($value)
    {
        return $this->setParameter('installments', $value);
    }

    protected function createResponse($data)
    {
        return $this->response = new Response($this, $data);
    }

    protected function getEndpoint()
    {
        return $this->getTestMode() ? ($this->testEndpoint . '/v1/payments/credit') : ($this->liveEndpoint . '/v1/payments/credit');
    }

}

?>
