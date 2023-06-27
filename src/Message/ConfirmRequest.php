<?php


/**
 * 交易确认
 */

namespace Omnipay\MyCard\Message;


class ConfirmRequest extends AbstractRequest
{

    public function getData()
    {
        $endpoint    = $this->getEndpoint( 'b2b' ).'/MyBillingPay/api/PaymentConfirm';
        $requestData = [
            'AuthCode' => $this->getToken()
        ];
        $response    = $this->httpClient->request( 'POST',$endpoint,[],http_build_query( $requestData ) );
        $data        = json_decode( $response->getBody()->getContents(),true );
        return $data;
    }


    public function sendData($data)
    {
        return new ConfirmResponse( $this,$data );
    }

}