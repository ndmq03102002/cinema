<?php

namespace Tests\Feature\Controller;

use App\Http\Controllers\Api\APP\RatingController;
use App\Http\Requests\RatingRequest;
use App\Models\MerchandiseRating;
use App\Models\Merchandises;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class RatingControllerTest extends TestCase
{
    public function setUp() : void
    {
        $this->afterApplicationCreated(function () {
            $this->merchandiseRating = new MerchandiseRating();
            $this->merchandises = new Merchandises();
            $this->user = new User();

            $this->ratingServiceMock = new RatingService(
                $this->merchandiseRating,
                $this->merchandises,
                $this->user
            );

            $this->ratingController = new RatingController(
                $this->app->instance(RatingService::class, $this->ratingServiceMock)
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
    public function testRatingRatingMerchandise()
    {
        DB::beginTransaction();

        $request = new RatingRequest();
        $request->rate = 5;
        $request->comment = 'unnittest';

        $fakeUser = $this->user->select('id', 'email')->first();

        $merchandise = $this->merchandises->where('giver_id', '!=', $fakeUser->id)->first();
        if (!$merchandise) {
            return false;
        }
        $merchandiseId = $merchandise->id;

        $credentials = [
            'email' => $fakeUser->email,
            'password' => 123123
        ];

        Auth::attempt($credentials);

        $res = $this->ratingController->ratingMerchandise($request, $merchandiseId);

        $this->assertEquals(200, $res->original['status']);

        DB::rollBack();
    }

    /**
     * @author Nampx
     */
    public function testRatingRatingUser()
    {
        DB::beginTransaction();

        $user = $this->user->orderBy('id', 'desc')->first();
        if (!$user) {
            return false;
        }
        $userId = $user->id;

        $request = new RatingRequest();
        $request->rate = 5;
        $request->comment = 'unnittest';
        $request->user_id = $userId;

        $merchandise = $this->merchandises->first();
        if (!$merchandise) {
            return false;
        }
        $merchandiseId = $merchandise->id;

        $fakeUser = $this->user->select('email')->first();

        $credentials = [
            'email' => $fakeUser->email,
            'password' => 123123
        ];

        Auth::attempt($credentials);

        $res = $this->ratingController->ratingUser($request, $merchandiseId);

        $this->assertEquals(200, $res->original['status']);

        DB::rollBack();
    }
}
