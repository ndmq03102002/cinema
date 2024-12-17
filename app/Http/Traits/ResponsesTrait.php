<?php

namespace App\Http\Traits;

use App\Enums\Constant;
use Illuminate\Http\JsonResponse;

trait ResponsesTrait
{

    /**
     * @var string
     */
    protected $message;

    /**
     * @var int
     */
    protected $code = Constant::SUCCESS_CODE;

    /**
     * @var array|string
     */
    protected $data = [];

    /**
     * @param $message
     * @return $this
     */
    public function setMessage($message): static
    {
        $this->message = $message;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMessage(): mixed
    {
        return $this->message;
    }

    /**
     * @param $code
     * @return $this
     */
    public function setCode($code): static
    {
        $this->code = $code;
        return $this;
    }

    /**
     * @param $data
     * @return $this
     */
    public function setData($data): static
    {
        $this->data = $data;
        return $this;
    }

    /**
     * @return JsonResponse
     */
    public function responseJson(): JsonResponse
    {
        return response()->json([
            'status' => $this->code,
            'message' => $this->message,
            'data' => $this->data
        ], $this->code);
    }

}
