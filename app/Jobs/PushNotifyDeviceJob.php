<?php

namespace App\Jobs;

use App\Enums\Constant;
use App\Models\Notify;
use App\Services\NotifyService;
use GuzzleHttp\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Response;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class PushNotifyDeviceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private mixed $receiverId;
    private mixed $notify;

    public function __construct(
        $receiverId,
        $notify,
    )
    {
        $this->receiverId = $receiverId;
        $this->notify = $notify;
    }

    public function handle()
    {
        if (!empty($this->receiverId)) {

            $data = [
                'registration_ids' => $this->receiverId,
                'notification' => [
                    'body' => $this->notify['content'],
                    'title' => $this->notify['title'],
                    'sound' => 'default'
                ],
                'data' => $this->notify,
                "contentAvailable" => true,
                "priority" => 'high',
            ];
            $client = new Client();

            $result = $client->request('POST', Constant::FCM_FIREBASE_URI, [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'key=' . env('FCM_SERVER_KEY'),
                ],
                'json' => $data,
                'timeout' => 300,
            ]);

            $result = $result->getStatusCode() == Response::HTTP_OK;

            Log::debug($result);

        } else {
            Log::debug($this->receiverId);

            return false;
        }
    }
}
