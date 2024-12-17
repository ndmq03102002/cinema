<?php

namespace App\Http\Traits;

use App\Enums\Constant;
use Illuminate\Support\Facades\Log;

trait GatewayApiTrait
{
    use ResponsesTrait;

    /**
     * @param       $url
     * @param       $method
     * @param       $data
     * @param array $headers
     * @return false|mixed
     */
    public function makeCurlReq($url, $method, $data, array $headers = [])
    {
        try {
            $ch = curl_init();

            if ($method == 'GET' && ! empty($data)) {
                $url .= '?' . http_build_query($data);
            } elseif ($method == 'POST') {
                curl_setopt_array($ch, [
                    CURLOPT_POST       => true,
                    CURLOPT_POSTFIELDS => $data,
                ]);
            }

            curl_setopt_array($ch, [
                CURLOPT_URL            => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER     => $headers,
                CURLOPT_TIMEOUT        => 5,
                CURLOPT_CONNECTTIMEOUT => 5,
            ]);

            $response   = curl_exec($ch);
            $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            if (curl_errno($ch)) {
                $this->setMessage(curl_error($ch))->setCode($statusCode);

                return false;
            }

            return json_decode($response);
        } catch (\Exception $e) {
            Log::error('Call Curl Failed: ' . $e->getMessage());
            $this->setMessage($e->getMessage())->setCode(Constant::BAD_REQUEST_CODE);

            return false;
        }
    }

    /**
     * @param       $url
     * @param       $method
     * @param       $data
     * @param array $headers
     * @return false|mixed
     */
    public function executedApi($url, $method, $data, array $headers = [])
    {
        $response = $this->makeCurlReq($url, $method, $data, $headers);

        if ($response) {

            if ($this->isSuccess($response)) {
                return $this->getData($response);
            }

            $this->setMessage($this->getMessageRes($response))->setCode($this->getCode($response));

            return false;
        }

        return false;
    }

    /**
     * @param $response
     * @return bool
     */
    private function isSuccess($response): bool
    {
        return $this->getCode($response) == Constant::SUCCESS_CODE;
    }

    /**
     * @param $response
     * @return mixed
     */
    private function getMessageRes($response)
    {
        return $response->message;
    }

    /**
     * @param $response
     * @return mixed
     */
    private function getData($response)
    {
        return $response->data;
    }

    /**
     * @param $response
     * @return mixed
     */
    private function getCode($response)
    {
        return $response->status_code;
    }
}
