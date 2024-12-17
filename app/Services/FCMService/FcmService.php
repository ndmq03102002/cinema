<?php

namespace App\Services\FCMService;

use App\Jobs\PushNotifyDeviceJob;
use Illuminate\Support\Facades\Log;

class FcmService
{
    public function pushNotify($receiverId, $notify): void
    {
        Log::debug('Tiến trình gửi thông báo: ' . json_encode($receiverId));
        Log::debug('Nội dung: ' . json_encode($notify));
        PushNotifyDeviceJob::dispatch(
            $receiverId,
            $notify
        );
    }
}
