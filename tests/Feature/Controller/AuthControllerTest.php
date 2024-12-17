<?php

namespace Tests\Feature\Controller;

use App\Http\Controllers\Api\APP\AuthController;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\Blocked;
use App\Models\Category;
use App\Models\Merchandises;
use App\Models\Messages;
use App\Models\PasswordReset;
use App\Models\Reported;
use App\Models\Transaction;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    public function setUp() : void
    {
        $this->afterApplicationCreated(function () {
            $this->category = new Category();
            $this->passwordReset = new PasswordReset();
            $this->user = new User();
            $this->messages = new Messages();
            $this->transaction = new Transaction();
            $this->merchandises = new Merchandises();
            $this->reported = new Reported();
            $this->blocked = new Blocked();

            $this->userServiceMock = new UserService(
                $this->passwordReset,
                $this->user,
                $this->messages,
                $this->transaction,
                $this->merchandises,
                $this->reported,
                $this->blocked,
            );

            $this->authController = new AuthController(
                $this->app->instance(UserService::class, $this->userServiceMock),
                $this->app->instance(User::class, $this->user)
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
    public function testAuthCreateUser()
    {
        DB::beginTransaction();

        $request = new RegisterRequest();
        $request->email = 'unittest@gmail.com';
        $request->display_name = 'unittest';
        $request->password = 123123;

        $res = $this->authController->createUser($request);

        $this->assertEquals(200, $res->original['status']);

        DB::rollBack();
    }

    /**
     * @author Nampx
     */
    public function testAuthCodeVerify()
    {
        DB::beginTransaction();

        $this->user->create([
            'email' => 'unittest@gmail.com',
            'display_name' => 'unittest123',
            'password' => Hash::make(123123),
            'status' => User::$active,
            'role_id' => User::$user,
        ]);

        $code = $this->passwordReset->create([
            'email' => 'unittest@gmail.com',
            'code' => 1234,
            'type' => User::$register_code,
        ]);

        $request = new Request();
        $request->email = $code->email;
        $request->code = $code->code;

        $res = $this->authController->codeVerify($request);

        $this->assertEquals(200, $res->original['status']);

        DB::rollBack();
    }

    /**
     * @author Nampx
     */
    public function testAuthLoginUser()
    {
        DB::beginTransaction();

        $user = $this->user->create([
            'email' => 'unittest123@gmail.com',
            'display_name' => 'unittest123',
            'password' => Hash::make(123123),
            'status' => User::$active,
            'role_id' => User::$user,
            'device_token' => 'device_token_test',
        ]);

        $request = new LoginRequest();
        $request->email = $user->email;
        $request->password = 123123;
        $request->device_token = $user->device_token;

        $res = $this->authController->loginUser($request);

        $this->assertEquals(200, $res->original['status']);

        DB::rollBack();
    }

    /**
     * @author Nampx
     */
    public function testAuthLoginAdmin()
    {
        DB::beginTransaction();

        $user = $this->user->create([
            'email' => 'unittest123@gmail.com',
            'display_name' => 'unittest123',
            'password' => Hash::make(123123),
            'status' => User::$active,
            'role_id' => User::$admin,
            'device_token' => 'device_token_test',
        ]);

        $request = new LoginRequest();
        $request->email = $user->email;
        $request->password = 123123;
        $request->device_token = 123123;

        $res = $this->authController->loginAdmin($request);

        $this->assertEquals(200, $res->original['status']);

        DB::rollBack();
    }
}
