<?php

namespace App\Jobs;

use App\Services\FirebaseService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class MarkReadNotifyJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $userId;
    public $notifies;

    public function __construct(
        $userId,
        $notifies,
    )
    {
        $this->userId = $userId;
        $this->notifies = $notifies;
    }

    public function handle(FirebaseService $firebaseService)
    {
        foreach ($this->notifies as $key => $notify) {
            $firebaseService->firebase->getReference($firebaseService->pathAPI . $this->userId)
                ->getChild($key)
                ->getChild('read')
                ->set(true);
        }
    }
}
