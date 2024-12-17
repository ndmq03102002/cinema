<?php

namespace App\Services\PaymentService\ShopeePay;

use App\Enums\Constant;
use App\Helpers\CommonHelper;

class ShoppePaymentService
{

    protected $merchant_ext_id;
    protected $store_ext_id;
    protected $client_id;
    protected $secret;
    protected $platform;
    protected $return_url_web;
    protected $return_url_app;
    protected $defaultParameter = [
        'merchant_ext_id',
        'store_ext_id'
    ];
    protected $return_url = 'return_url_';

    /**
     * @param array $data
     * @return void
     */
    public function initialize(array $data)
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
    }

    /**
     * @param $data
     * @return mixed
     */
    protected function getDefaultParameter($data)
    {
        foreach ($this->defaultParameter as $key) {
            if ($this->$key) {
                $data[$key] = $this->$key;
            }
        }

        return $data;
    }

    /**
     * @return string
     */
    public function getReturnUrl(): string
    {
        $typeReturnUrl = $this->return_url . (in_array($this->platform, ['mweb', 'pc']) ? 'web' : 'app');
        return $this->return_url = $this->$typeReturnUrl;
    }

    /**
     * @param $platform
     */
    public function setPlatform($platform)
    {
        $this->platform = $platform;
    }

    /**
     * @param $data
     * @return mixed
     */
    public function purchase($data)
    {
        $data = $this->getDefaultParameter($data);
        $data['return_url'] = $this->getReturnUrl();
        $signature = $this->getSignature($data, $this->secret);
        $reqHeader = $this->getHeaderReq($this->client_id, $signature);
        $res_json = CommonHelper::makeCurlRequest(Constant::SPP_CREATE_ORDER, 'POST', json_encode($data), $reqHeader);

        return json_decode($res_json);
    }

    /**
     * @param $data
     * @return mixed
     */
    public function invalidOrder($data)
    {
        $data = $this->getDefaultParameter($data);
        $signature = $this->getSignature($data, $this->secret);
        $reqHeader = $this->getHeaderReq($this->client_id, $signature);
        $res_json = CommonHelper::makeCurlRequest(Constant::SPP_DISABLE_ORDER, 'POST', json_encode($data), $reqHeader);
        return json_decode($res_json);
    }

    /**
     * @param $data
     * @param $secret
     * @return string
     */
    protected function getSignature($data, $secret): string
    {
        return base64_encode(hash_hmac('sha256', json_encode($data), $secret, true));
    }

    /**
     * @param $data
     * @return string
     */
    public function signatureTrans($data): string
    {
        $data = $this->getDefaultParameter($data);
        return base64_encode(hash_hmac('sha256', json_encode($data), $this->secret, true));
    }

    /**
     * @param $client_id
     * @param $signature
     * @return string[]
     */
    protected function getHeaderReq($client_id, $signature): array
    {
        return [
            "Content-Type: application/json",
            "X-Airpay-ClientId:{$client_id}",
            "X-Airpay-Req-H: {$signature}",
        ];
    }

    /**
     * @param $response
     * @return bool
     */
    public function isSuccess($response): bool
    {
        return $response->errcode == 0;
    }

    /**
     * @param $response
     * @return string|null
     */
    public function getUrl($response): ?string
    {
        return $response->redirect_url_http;
    }

    /**
     * @param $response
     * @return string|null
     */
    public function getReferenceId($response): ?string
    {
        parse_str(parse_url($response->redirect_url_http, PHP_URL_QUERY), $queryParams);
        return $queryParams['order_id'];
    }

    /**
     * @param $response
     * @return mixed
     */
    public function getRequestId($response)
    {
        return $response->request_id;
    }

    public function getSecret()
    {
        return $this->secret;
    }

}
