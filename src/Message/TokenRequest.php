<?php

namespace Omnipay\MyCard\Message;


use Omnipay\MyCard\Exception\DefaultException;

class TokenRequest extends AbstractRequest
{


    public function getToken()
    {
        $endpoint = $this->getEndpoint('b2b') . '/MyBillingPay/api/AuthGlobal';
        $requestData = [
            'FacServiceId' => $this->getAppId(),
            'FacTradeSeq'  => $this->getTransactionId(),
            'TradeType'    => $this->getTradeType(),
            'ServerId'     => $this->getServerId() ?: '',                         // 服务器ID
            'CustomerId'   => $this->getAccountId() ?: $this->getTransactionId(), // 用户ID
            'PaymentType'  => $this->getPaymentType() ?: '',
            'ItemCode'     => $this->getItemCode() ?: '',
            'ProductName'  => $this->getDescription(),
            'Amount'       => $this->getAmount(),
            'Currency'     => $this->getCurrency(),
            'SandBoxMode'  => $this->getTestMode() ? 'true' : 'false',
            'Hash'         => $this->getSign('token'),
        ];
        $requestData = array_filter($requestData);
        $httpResponse=   $this->httpClient->request('POST', $endpoint, [], http_build_query($requestData));
        $body = json_decode($httpResponse->getBody()->getContents(), true);
        if ($body['ReturnCode'] != 1) {
            throw new DefaultException($body['ReturnMsg']);
        }
        return $body;
    }


    public function getSign($type = '')
    {
        $sandboxMode = $this->getTestMode() ? 'true' : 'false';
        $preSign = '';

        switch ($type) {

            case 'token':
                $preSign =
                    $this->getAppId() .
                    $this->getTransactionId() .
                    $this->getTradeType() .
                    $this->getServerId() .
                    ($this->getAccountId() ?: $this->getTransactionId()) .
                    $this->getPaymentType() .
                    $this->getItemCode() .
                    strtolower(urlencode($this->getDescription())) .
                    $this->getAmount() .
                    $this->getCurrency() .
                    $sandboxMode .
                    $this->getAppKey();
                break;

            case 'returnHash':
                $preSign = $this->httpRequest->get('ReturnCode') .
                    $this->httpRequest->get('PayResult') .
                    $this->httpRequest->get('FacTradeSeq') .
                    $this->httpRequest->get('PaymentType') .
                    $this->httpRequest->get('Amount') .
                    $this->httpRequest->get('Currency') .
                    $this->httpRequest->get('MyCardTradeNo') .
                    $this->httpRequest->get('MyCardType') .
                    $this->httpRequest->get('PromoCode') .
                    $this->getAppKey();
                break;

        }

        return hash('sha256', $preSign);
    }

}