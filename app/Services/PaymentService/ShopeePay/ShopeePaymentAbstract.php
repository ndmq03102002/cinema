<?php

namespace App\Services\PaymentService\ShopeePay;

use App\Models\Ticket;
use App\Services\PaymentService\PaymentTrait\PaymentMessageErrorTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

abstract class ShopeePaymentAbstract
{
    use PaymentMessageErrorTrait;

    /**
     * @var ShoppePaymentService
     */
    protected $gatewayService;

    public function __construct()
    {
        $gatewayService = new ShoppePaymentService();
        $this->gatewayService = $gatewayService;

        $this->gatewayService->initialize([
            'merchant_ext_id' => config('payment.shopee.merchant_id'),
            'store_ext_id' => config('payment.shopee.merchant_id'),
            'secret' => config('payment.shopee.secret'),
            'client_id' => config('payment.shopee.client_id'),
            'return_url_web' => config('payment.shopee.return_url_web'),
            'return_url_app' => config('payment.shopee.return_url_app'),
        ]);

    }

    /**
     * @param Request $request
     * @param Ticket $ticket
     * @return mixed
     */
    abstract public function makePayment(Request $request, Ticket $ticket);

    /**
     * @param $reference_id
     * @param $ticket_id
     * @return mixed
     */
    abstract public function disablePayment($reference_id, $ticket_id);

    /**
     * @param Request $request
     * @param Ticket $ticket
     * @return false|mixed
     */
    public function executePayment(Request $request, Ticket $ticket)
    {
        try {
            return $this->makePayment($request, $ticket);
        } catch (\Exception $e) {
            $this->setErrorMessage($e->getMessage());
            Log::error($e->getMessage());
            return false;
        }
    }

    /**
     * @param $reference_id
     * @param $ticket_id
     * @return mixed
     */
    public function executeInvalidPayment($reference_id, $ticket_id)
    {
        try {
            return $this->disablePayment($reference_id, $ticket_id);
        } catch (\Exception $e) {
            $this->setErrorMessage($e->getMessage());
            Log::error($e->getMessage());
            return false;
        }
    }

}
