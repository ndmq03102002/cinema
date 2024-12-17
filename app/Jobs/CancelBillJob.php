<?php

namespace App\Jobs;

use App\Http\Controllers\Api\APP\BillController;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;


class CancelBillJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $billController;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        BillController $billController
    )
    {
        $this->billController = $billController;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->billController->cancelBill();
    }
}
