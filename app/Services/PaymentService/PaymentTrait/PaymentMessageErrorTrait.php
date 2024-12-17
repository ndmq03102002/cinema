<?php

namespace App\Services\PaymentService\PaymentTrait;

trait PaymentMessageErrorTrait {

    protected $errorMessage;

    /**
     * @return string|null
     */
    public function getErrorMessage(): ?string
    {
        return $this->errorMessage;
    }

    /**
     * @param $errorMessage
     */
    public function setErrorMessage($errorMessage)
    {
        $this->errorMessage = $errorMessage;
    }
}
