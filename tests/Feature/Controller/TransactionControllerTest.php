<?php

namespace Tests\Feature\Controller;

use App\Http\Controllers\Api\TransactionController;
use App\Http\Requests\TransactionRequest;
use App\Models\Category;
use App\Models\MerchandiseRating;
use App\Models\Merchandises;
use App\Models\Transaction;
use App\Models\User;
use App\Services\FirebaseService;
use App\Services\TransactionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class TransactionControllerTest extends TestCase
{
    public function setUp() : void
    {
        $this->afterApplicationCreated(function () {
            $this->merchandises = new Merchandises();
            $this->transaction = new Transaction();
            $this->category = new Category();
            $this->merchandiseRating = new MerchandiseRating();
            $this->user = new User();

            $this->firebaseServiceMock = new FirebaseService();

            $this->transactionServiceMock = new TransactionService(
                $this->merchandises,
                $this->transaction,
                $this->merchandiseRating,
                $this->user,
            );

            $this->transactionController = new TransactionController(
                $this->app->instance(TransactionService::class, $this->transactionServiceMock ),
                $this->app->instance(FirebaseService::class, $this->firebaseServiceMock ),
                $this->app->instance(Transaction::class, $this->transaction),
                $this->app->instance(Merchandises::class, $this->merchandises),
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
     * @author Nampx
     */
    public function testTransactionIndex()
    {
        DB::beginTransaction();

        $res = $this->transactionController->index();

        $this->assertEquals(200, $res->original['status']);

        DB::rollBack();
    }

    /**
     * @author Nampx
     */
    public function testTransactionMerchandises()
    {
        DB::beginTransaction();

        $category = $this->category->first();
        if (!$category) {
            return false;
        }
        $categoryId = $category->id;

        $fakeUser = $this->user->select('id', 'email')->first();

        $credentials = [
            'email' => $fakeUser->email,
            'password' => 123123
        ];

        Auth::attempt($credentials);

        $user = $this->user->orderBy('id', 'desc')->first();
        if (!$user) {
            return false;
        }
        $userId = $user->id;

        $merchandises = $this->merchandises->create([
            'name' => 'unittest_name',
            'description' => 'unittest_description',
            'category_id' => $categoryId,
            'condition' => 'unittest_condition',
            'amount' => 100,
            'giver_id' => auth('sanctum')->user()->id
        ]);
        if (!$merchandises) {
            return false;
        }
        $merchandiseId = $merchandises->id;

        $request = new TransactionRequest();
        $request->receiver_id = json_encode([$userId]);
        $request->merchandise_id = $merchandiseId;
        $request->amount = 2;

        $res = $this->transactionController->transactionMerchandises($request);

        $this->assertEquals(200, $res->original['status']);

        DB::rollBack();
    }
}
