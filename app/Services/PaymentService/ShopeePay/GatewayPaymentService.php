<?php

namespace App\Services\PaymentService\ShopeePay;

use App\Models\Ticket;
use App\Models\Transaction;
use App\Services\PaymentService\ShopeePay\ShopeePaymentAbstract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class GatewayPaymentService extends ShopeePaymentAbstract
{

    /**
     * @param Request $request
     * @param Ticket $ticket
     * @return false|string|null
     */
    public function makePayment(Request $request, Ticket $ticket)
    {
        try {
            if ($ticket->paid()) {
                $this->setErrorMessage('Tickets have been paid');
                return false;
            }

            if (!$this->checkSignatureBeforePayment($request, $ticket)) {
                $this->setErrorMessage("Signature not match!");
                return false;
            }

            $this->gatewayService->setPlatform($request->input('platform_type'));
            $response = $this->gatewayService->purchase($this->getData($request, $ticket));
            if ($this->gatewayService->isSuccess($response)) {
                $this->saveTransaction($ticket, null, [
                    'reference_id' => $this->gatewayService->getReferenceId($response),
                    'request_id' => $this->gatewayService->getRequestId($response),
                    'payment_id' => $request->input('payment_id')
                ]);

                return $this->gatewayService->getUrl($response);
            }
            $this->setErrorMessage($response->debug_msg);
            return false;
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            $this->setErrorMessage($e->getMessage());
            return false;
        }
    }

    /**
     * @param $request
     * @param Ticket $ticket
     * @return array
     */
    public function getData($request, Ticket $ticket): array
    {
        return [
            'request_id' => generateRequestId($ticket->id),
            'amount' => $ticket->getAmount() * 100,
            'currency' => 'VND',
            'payment_reference_id' => generateOrderCode($ticket->id),
            'platform_type' => $request->input('platform_type'),
        ];
    }

    /**
     * @param Ticket $ticket
     * @param int|null $amount
     * @param array $attributes
     * @return Model
     */
    public function saveTransaction(Ticket $ticket, ?int $amount = null, array $attributes = []): Model
    {
        return $ticket->pay($amount, $attributes);
    }

    /**
     * @param $reference_id
     * @param $ticket_id
     * @return false|mixed
     */
    public function disablePayment($reference_id, $ticket_id)
    {
        $response = $this->gatewayService->invalidOrder([
            'request_id' => generateRequestId($ticket_id),
            'payment_reference_id' => $reference_id,
        ]);

        if ($this->gatewayService->isSuccess($response)) {
            return $response;
        }

        Log::error('Disable error', ['res' => $response, 'reference_id' => $reference_id]);

        return false;
    }

    /**
     * @param $data
     * @return string
     */
    public function getSignatureTransServe($data): string
    {
        $trans = Transaction::where('reference_id', $data['reference_id'])->select(['reference_id', 'amount'])->first();
        return $this->gatewayService->signatureTrans([
            'reference_id' => $trans->reference_id,
            'amount' => $trans->amount * 100
        ]);
    }

    /**
     * @param $data
     * @return bool
     */
    public function checkSignIpn($data): bool
    {
        return $this->getTransReq($data) === $this->getSignatureTransServe($data);
    }

    /**
     * @param $data
     * @return string
     */
    public function getTransReq($data): string
    {
        $data = Arr::only($data, ['reference_id', 'amount', 'store_ext_id', 'merchant_ext_id']);

        return base64_encode(hash_hmac('sha256', json_encode($data), $this->gatewayService->getSecret(), true));
    }

    /**
     * @param $request
     * @param Ticket $ticket
     * @return bool
     */
    public function checkSignatureBeforePayment($request, Ticket $ticket): bool
    {
        $apiKey = config('app.tc_api_key');
        $data = $request->only(['payment_id', 'platform_type', 'ticket_id', 'payment_gateway']);
        $data = array_merge($data, ['amount' => $ticket->getAmount()]);

        return base64_encode(hash_hmac('sha256', json_encode($data), $apiKey, true)) == $request->header('signature');
    }
}

