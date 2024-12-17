<?php

namespace App\Jobs;

use App\Services\NotifyService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class NotifyJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $users;
    public $merchandise;
    public $author;
    public $typeJob;

    public function __construct(
        $users,
        $merchandise,
        $author,
        $typeJob
    )
    {
        $this->users = $users;
        $this->merchandise = $merchandise;
        $this->author = $author;
        $this->typeJob = $typeJob;
    }

    public function handle(NotifyService $notifyService)
    {
        foreach ($this->users as $user) {
            $notifyService->store($user, $this->typeJob, $this->author, $this->merchandise);
        }
    }
}
