<?php

namespace Tests\Feature\Controller;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Http\Controllers\Api\UserController;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\RecendCodeRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\UserEditRequest;
use App\Models\Blocked;
use App\Models\District;
use App\Models\Merchandises;
use App\Models\Messages;
use App\Models\PasswordReset;
use App\Models\Province;
use App\Models\Reported;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Ward;
use App\Services\FileUploadServices\FileService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    public function setUp() : void
    {
        $this->afterApplicationCreated(function () {
            $this->user = new User();
            $this->passwordReset = new PasswordReset();
            $this->messages = new Messages();
            $this->transaction = new Transaction();
            $this->merchandises = new Merchandises();
            $this->province = new Province();
            $this->district = new District();
            $this->ward = new Ward();
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

            $this->fileServiceMock = new FileService();

            $this->userController = new UserController(
                $this->app->instance(UserService::class, $this->userServiceMock),
                $this->app->instance(User::class, $this->user),
                $this->app->instance(FileService::class, $this->fileServiceMock),
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
    public function testUserIndex()
    {
        DB::beginTransaction();

        $res = $this->userController->index();

        $this->assertEquals(200, $res->original['status']);

        DB::rollBack();
    }

    /**
     * @author Nampx
     */
    public function testUserShow()
    {
        DB::beginTransaction();

        $user = $this->user->first();
        if (!$user) {
            return false;
        }
        $userId = $user->id;

        $res = $this->userController->show($userId);

        $this->assertEquals(200, $res->original['status']);

        DB::rollBack();
    }

    /**
     * @author Nampx
     */
    public function testUserEditProfile()
    {
        DB::beginTransaction();

        $province = $this->province->first();
        if (!$province) {
            return false;
        }
        $provinceId = $province->id;

        $district = $this->district->first();
        if (!$district) {
            return false;
        }
        $districtId = $district->id;

        $ward = $this->ward->first();
        if (!$ward) {
            return false;
        }
        $wardId = $ward->id;

        $request = new UserEditRequest();
        $request->display_name = 'unittest5';
        $request->phone_number = 12312312312;
        $request->province_code = $provinceId;
        $request->district_code = $districtId;
        $request->ward_code = $wardId;

        $user = $this->user->create([
            'email' => 'unittest123@gmail.com',
            'display_name' => 'unittest123',
            'password' => Hash::make(123123),
            'status' => User::$active,
            'role_id' => User::$user,
        ]);

        $credentials = [
            'email' => $user->email,
            'password' => 123123
        ];

        Auth::attempt($credentials);

        $res = $this->userController->editProfile($request);

        $this->assertEquals(200, $res->original['status']);

        DB::rollBack();
    }

    /**
     * @author Nampx
     */
    public function testUserEditAvatar()
    {
        DB::beginTransaction();

        $request = new Request();
        $request->image_data = new \Illuminate\Http\UploadedFile(resource_path('unittest.jpg'), 'unittest.jpg', null, null, true);

        $fakeUser = $this->user->select('email')->first();

        $credentials = [
            'email' => $fakeUser->email,
            'password' => 123123
        ];

        Auth::attempt($credentials);

        $res = $this->userController->editAvatar($request);

        $this->assertEquals(200, $res->original['status']);

        DB::rollBack();
    }

    /**
     * @author Nampx
     */
    public function testUserChangePassword()
    {
        DB::beginTransaction();

        $request = new Request();
        $request->old_password = 123123;
        $request->new_password = 123456;

        $user = $this->user->create([
            'email' => 'unittest123@gmail.com',
            'display_name' => 'unittest123',
            'password' => Hash::make(123123),
            'status' => User::$active,
            'role_id' => User::$user,
        ]);

        $credentials = [
            'email' => $user->email,
            'password' => 123123
        ];

        Auth::attempt($credentials);

        $res = $this->userController->changePassword($request);

        $this->assertEquals(200, $res->original['status']);

        DB::rollBack();
    }

    /**
     * @author Nampx
     */
    public function testUserDestroy()
    {
        DB::beginTransaction();

        $user = $this->user->create([
            'email' => 'unittest123@gmail.com',
            'display_name' => 'unittest123',
            'password' => Hash::make(123123),
            'status' => User::$active,
            'role_id' => User::$user,
        ]);

        $credentials = [
            'email' => $user->email,
            'password' => 123123
        ];

        Auth::attempt($credentials);

        $res = $this->userController->destroy();

        $this->assertEquals(200, $res->original['status']);

        DB::rollBack();
    }

    /**
     * @author Nampx
     */
    public function testUserMyAccount()
    {
        DB::beginTransaction();

        $user = $this->user->create([
            'email' => 'unittest123@gmail.com',
            'display_name' => 'unittest123',
            'password' => Hash::make(123123),
            'status' => User::$active,
            'role_id' => User::$user,
        ]);

        $credentials = [
            'email' => $user->email,
            'password' => 123123
        ];

        Auth::attempt($credentials);

        $res = $this->userController->myAccount();

        $this->assertEquals(200, $res->original['status']);

        DB::rollBack();
    }

    /**
     * @author Nampx
     */
    public function testUserSendCodeForgotPassword()
    {
        DB::beginTransaction();

        $user = $this->user->create([
            'email' => 'unittest123@gmail.com',
            'display_name' => 'unittest123',
            'password' => Hash::make(123123),
            'status' => User::$active,
            'role_id' => User::$user,
        ]);

        $credentials = [
            'email' => $user->email,
            'password' => 123123
        ];

        Auth::attempt($credentials);

        $request = new ForgotPasswordRequest();
        $request->email = $user->email;

        $res = $this->userController->sendCodeForgotPassword($request);

        $this->assertEquals(200, $res->original['status']);

        DB::rollBack();
    }

    /**
     * @author Nampx
     */
    public function testUserReSendCode()
    {
        DB::beginTransaction();

        $user = $this->user->create([
            'email' => 'unittest123@gmail.com',
            'display_name' => 'unittest123',
            'password' => Hash::make(123123),
            'status' => User::$active,
            'role_id' => User::$user,
        ]);

        $credentials = [
            'email' => $user->email,
            'password' => 123123
        ];

        Auth::attempt($credentials);

        $request = new RecendCodeRequest();
        $request->email = $user->email;
        $request->type = User::$forgot_code;

        $res = $this->userController->reSendCode($request);

        $this->assertEquals(200, $res->original['status']);

        DB::rollBack();
    }

    /**
     * @author Nampx
     */
    public function testUserCodeVerify()
    {
        DB::beginTransaction();

        $user = $this->user->create([
            'email' => 'unittest123@gmail.com',
            'display_name' => 'unittest123',
            'password' => Hash::make(123123),
            'status' => User::$active,
            'role_id' => User::$user,
        ]);

        $credentials = [
            'email' => $user->email,
            'password' => 123123
        ];

        Auth::attempt($credentials);

        $code = $this->passwordReset->create([
            'email' => $user->email,
            'code' => 1234,
            'type' => User::$forgot_code,
        ]);

        $request = new ForgotPasswordRequest();
        $request->email = $code->email;
        $request->code = $code->code;

        $res = $this->userController->codeVerify($request);

        $this->assertEquals(200, $res->original['status']);

        DB::rollBack();
    }

    /**
     * @author Nampx
     */
    public function testUserResetPassword()
    {
        DB::beginTransaction();

        $user = $this->user->create([
            'email' => 'unittest123@gmail.com',
            'display_name' => 'unittest123',
            'password' => Hash::make(123123),
            'status' => User::$active,
            'role_id' => User::$user,
        ]);

        $credentials = [
            'email' => $user->email,
            'password' => 123123
        ];

        Auth::attempt($credentials);

        $code = $this->passwordReset->create([
            'email' => $user->email,
            'code' => 1234,
            'type' => User::$forgot_code,
        ]);

        $request = new ResetPasswordRequest();
        $request->email = $code->email;
        $request->code = $code->code;
        $request->new_password = 123123;

        $res = $this->userController->resetPassword($request);

        $this->assertEquals(200, $res->original['status']);

        DB::rollBack();
    }

    /**
     * @author Nampx
     */
    public function testUserGetUserInChatList()
    {
        DB::beginTransaction();

        $user = $this->user->create([
            'email' => 'unittest123@gmail.com',
            'display_name' => 'unittest123',
            'password' => Hash::make(123123),
            'status' => User::$active,
            'role_id' => User::$user,
        ]);

        $credentials = [
            'email' => $user->email,
            'password' => 123123
        ];

        Auth::attempt($credentials);

        $request = new Request();
        $request->page = 1;
        $request->keyword = 'a';

        $res = $this->userController->getUserInChatList($request);

        $this->assertEquals(200, $res->original['status']);

        DB::rollBack();
    }

    /**
     * @author Nampx
     */
    public function testUserLogout()
    {
        DB::beginTransaction();

        $user = $this->user->create([
            'email' => 'unittest123@gmail.com',
            'display_name' => 'unittest123',
            'password' => Hash::make(123123),
            'status' => User::$active,
            'role_id' => User::$user,
        ]);

        $credentials = [
            'email' => $user->email,
            'password' => 123123
        ];

        Auth::attempt($credentials);

        $res = $this->userController->logout();

        $this->assertEquals(200, $res->original['status']);

        DB::rollBack();
    }

    /**
     * @author Nampx
     */
    public function testUserMerchandiseByUserLogged()
    {
        DB::beginTransaction();

        $user = $this->user->create([
            'email' => 'unittest123@gmail.com',
            'display_name' => 'unittest123',
            'password' => Hash::make(123123),
            'status' => User::$active,
            'role_id' => User::$user,
        ]);

        $credentials = [
            'email' => $user->email,
            'password' => 123123
        ];

        Auth::attempt($credentials);

        $res = $this->userController->merchandiseByUserLogged();

        $this->assertEquals(200, $res->original['status']);

        DB::rollBack();
    }

    /**
     * @author Nampx
     */
    public function testUserMerchandiseByUserId()
    {
        DB::beginTransaction();

        $user = $this->user->create([
            'email' => 'unittest123@gmail.com',
            'display_name' => 'unittest123',
            'password' => Hash::make(123123),
            'status' => User::$active,
            'role_id' => User::$user,
        ]);

        $credentials = [
            'email' => $user->email,
            'password' => 123123
        ];

        Auth::attempt($credentials);

        $receiver = $this->user->first();
        $receiverId = $receiver->id;

        $res = $this->userController->merchandiseByUserId($receiverId);

        $this->assertEquals(200, $res->original['status']);

        DB::rollBack();
    }
}
