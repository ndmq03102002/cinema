<?php

namespace Tests\Feature\Controller\V2;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Http\Controllers\Api\V2\UserV2Controller;
use App\Http\Requests\V2\ResetPasswordV2Request;
use App\Http\Requests\V2\UserV2Request;
use App\Models\User;
use App\Services\V2\UserV2Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class UserV2ControllerTest extends TestCase
{
    public function setUp() : void
    {
        $this->afterApplicationCreated(function () {
            $this->user = new User();

            $this->userV2ServiceMock = new UserV2Service($this->user);

            $this->userV2Controller = new UserV2Controller(
                $this->app->instance(UserV2Service::class, $this->userV2ServiceMock),
                $this->app->instance(User::class, $this->user),
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
    public function testUserV2Index()
    {
        DB::beginTransaction();

        $request = new Request();
        $request->keyword = 'a';
        $request->status = User::$active;
        $request->perpage = 10;
        $res = $this->userV2Controller->index($request);

        $this->assertEquals(200, $res->original['status']);

        DB::rollBack();
    }

    /**
     * @author Nampx
     */
    public function testUserV2Show()
    {
        DB::beginTransaction();

        $user = $this->user->first();
        if (!$user) {
            return false;
        }
        $userId = $user->id;

        $res = $this->userV2Controller->show($userId);

        $this->assertEquals(200, $res->original['status']);

        DB::rollBack();
    }

    /**
     * @author Nampx
     */
    public function testUserV2EditProfile()
    {
        DB::beginTransaction();

        $request = new UserV2Request();
        $request->display_name = 'unittest1';
        $request->email = 'unittest@gmail.com';
        $request->phone_number = 12312312312312;
        $request->status = 1;
        $request->user_id = 1;
        $res = $this->userV2Controller->editProfile($request);

        $this->assertEquals(200, $res->original['status']);

        DB::rollBack();
    }

    /**
     * @author Nampx
     */
    public function testUserV2Destroy()
    {
        DB::beginTransaction();

        $user = $this->user->first();
        if (!$user) {
            return false;
        }
        $userId = $user->id;
        $res = $this->userV2Controller->destroy($userId);

        $this->assertEquals(200, $res->original['status']);
        DB::rollBack();
    }

    /**
     * @author Nampx
     */
    public function testUserV2LockUser()
    {
        DB::beginTransaction();

        $user = $this->user->first();
        if (!$user) {
            return false;
        }
        $userId = $user->id;

        $request = new Request();
        $request->status = User::$active;
        $res = $this->userV2Controller->lockUser($request, $userId);

        $this->assertEquals(200, $res->original['status']);
        DB::rollBack();
    }

    /**
     * @author Nampx
     */
    public function testUserV2ChangePassword()
    {
        DB::beginTransaction();

        $user = $this->user->first();
        if (!$user) {
            return false;
        }
        $userId = $user->id;

        $request = new ResetPasswordV2Request();
        $request->new_password = 123456;
        $res = $this->userV2Controller->changePassword($request, $userId);

        $this->assertEquals(200, $res->original['status']);
        DB::rollBack();
    }

    /**
     * @author Nampx
     */
    public function testUserV2AnalysisUser()
    {
        DB::beginTransaction();

        $request = new Request();
        $request->year = 2022;
        $request->month = 12;

        $res = $this->userV2Controller->analysisUser($request);

        $this->assertEquals(200, $res->original['status']);
        DB::rollBack();
    }
}
