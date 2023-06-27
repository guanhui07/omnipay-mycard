<?php


/**
 * 交易查询 @docs: 3.3
 */

namespace Omnipay\MyCard\Message;


class FetchRequest extends AbstractRequest
{

    public function getData()
    {
        $endpoint    = $this->getEndpoint( 'b2b' ).'/MyBillingPay/api/TradeQuery';
        $requestData = [
            'AuthCode' => $this->getToken()
        ];
        $response    = $this->httpClient->request( 'POST',$endpoint,[],http_build_query( $requestData ) );
        $data        = json_decode( $response->getBody()->getContents(),true );
        return $data;
    }


    public function sendData($data)
    {
        return new FetchResponse( $this,$data );
    }

}