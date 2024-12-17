<?php

namespace Tests\Feature\Controller\V2;

use App\Http\Controllers\Api\V2\TransactionV2Controller;
use App\Models\Transaction;
use App\Services\V2\TransactionV2Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class TransactionV2ControllerTest extends TestCase
{
    public function setUp() : void
    {
        $this->afterApplicationCreated(function () {
            $this->transaction = new Transaction();

            $this->transactionV2ServiceMock = new TransactionV2Service(
                $this->transaction,
            );

            $this->transactionV2Controller = new TransactionV2Controller(
                $this->app->instance(TransactionV2Service::class, $this->transactionV2ServiceMock),
            );
        });

        parent::setUp();

    }

    public function tearDown(): void
    {
        // Other tearing down ...
        \Mockery::close();
        parent::tearDown();
    }

    /**
     * @return void
     * @author Nampx
     */
    public function testTransactionsByYears()
    {
        DB::beginTransaction();

        $request = new Request();
        $request->year = 2022;

        $res = $this->transactionV2Controller->transactionsByYears($request);

        $this->assertEquals(200, $res->original['status']);

        DB::rollBack();
    }

    /**
     * @return void
     * @author Nampx
     */
    public function testTransactionsByQuarters()
    {
        DB::beginTransaction();

        $request = new Request();
        $request->year = 2022;
        $request->quarters = 1;

        $res = $this->transactionV2Controller->transactionsByQuarters($request);

        $this->assertEquals(200, $res->original['status']);

        DB::rollBack();
    }

    /**
     * @return void
     * @author Nampx
     */
    public function testTransactionsByMonths()
    {
        DB::beginTransaction();

        $request = new Request();
        $request->year = 2022;
        $request->month = 12;

        $res = $this->transactionV2Controller->transactionsByMonths($request);

        $this->assertEquals(200, $res->original['status']);

        DB::rollBack();
    }
}
