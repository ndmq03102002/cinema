<?php

namespace Tests\Feature\Controller;

use App\Enums\Constant;
use App\Http\Controllers\Api\NotifyController;
use App\Models\Notify;
use App\Models\User;
use App\Services\NotifyService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class NotifyControllerTest extends TestCase
{
    public function setUp() : void
    {
        $this->afterApplicationCreated(function () {
            $this->notify = new Notify();
            $this->user = new User();

            $this->notifyServiceMock = new NotifyService(
                $this->notify,
            );

            $this->notifyController = new NotifyController(
                $this->app->instance(NotifyService::class, $this->notifyServiceMock)
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
    public function testNotifyGetNotifyByUserId()
    {
        DB::beginTransaction();

        $fakeUser = $this->user->select('email')->first();

        $credentials = [
            'email' => $fakeUser->email,
            'password' => 123123
        ];

        Auth::attempt($credentials);

        $res = $this->notifyController->getNotifyByUserId();

        $this->assertEquals(200, $res->original['status']);

        DB::rollBack();
    }

    /**
     * @author Nampx
     */
    public function testNotifyMarkReadNotify()
    {
        DB::beginTransaction();

        $giver = $this->user->create([
            'email' => 'unittest1@gmail.com',
            'display_name' => 'unittest1',
            'password' => Hash::make(123123),
            'status' => User::$active,
            'role_id' => User::$user,
        ]);

        $receiver = $this->user->create([
            'email' => 'unittest2@gmail.com',
            'display_name' => 'unittest2',
            'password' => Hash::make(123123),
            'status' => User::$active,
            'role_id' => User::$user,
        ]);

        $notify = $this->notify->create([
            'giver_id' => $giver->id,
            'receiver_id' => $receiver->id,
            'read' => 0,
        ]);

        $notifyId = $notify->id;

        $request = new Request();
        $request->notify_id = $notifyId;


        $credentials = [
            'email' => $receiver->email,
            'password' => 123123
        ];

        Auth::attempt($credentials);

        $res = $this->notifyController->markReadNotify($request);

        $this->assertEquals(200, $res->original['status']);

        DB::rollBack();
    }

    /**
     * @author Nampx
     */
    public function testNotifyMarkReadAllNotify()
    {
        DB::beginTransaction();

        $giver = $this->user->create([
            'email' => 'unittest1@gmail.com',
            'display_name' => 'unittest1',
            'password' => Hash::make(123123),
            'status' => User::$active,
            'role_id' => User::$user,
        ]);

        $credentials = [
            'email' => $giver->email,
            'password' => 123123
        ];

        Auth::attempt($credentials);

        $res = $this->notifyController->markReadAllNotify();

        $this->assertEquals(200, $res->original['status']);

        DB::rollBack();
    }

    /**
     * @author Nampx
     */
    public function testNotifyDeleteNotify()
    {
        DB::beginTransaction();

        $giver = $this->user->create([
            'email' => 'unittest1@gmail.com',
            'display_name' => 'unittest1',
            'password' => Hash::make(123123),
            'status' => User::$active,
            'role_id' => User::$user,
        ]);

        $receiver = $this->user->create([
            'email' => 'unittest2@gmail.com',
            'display_name' => 'unittest2',
            'password' => Hash::make(123123),
            'status' => User::$active,
            'role_id' => User::$user,
        ]);

        $notify = $this->notify->create([
            'giver_id' => $giver->id,
            'receiver_id' => $receiver->id,
            'read' => 0,
        ]);

        $notifyId = $notify->id;

        $credentials = [
            'email' => $receiver->email,
            'password' => 123123
        ];

        Auth::attempt($credentials);

        $res = $this->notifyController->deleteNotify($notifyId);

        $this->assertEquals(200, $res->original['status']);

        DB::rollBack();
    }
}
