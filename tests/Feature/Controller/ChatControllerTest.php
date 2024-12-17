<?php

namespace Tests\Feature\Controller;

use App\Http\Controllers\Api\ChatController;
use App\Http\Requests\ChatRequest;
use App\Models\Blocked;
use App\Models\Merchandises;
use App\Models\MessageImages;
use App\Models\MessageRoom;
use App\Models\Messages;
use App\Models\RegisterGift;
use App\Models\User;
use App\Services\ChatService;
use App\Services\FileUploadServices\FileService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ChatControllerTest extends TestCase
{
    public function setUp() : void
    {
        $this->afterApplicationCreated(function () {
            $this->messageRoom = new MessageRoom();
            $this->merchandises = new Merchandises();
            $this->messageImages = new MessageImages();
            $this->messages = new Messages();
            $this->user = new User();
            $this->registerGift = new RegisterGift();
            $this->blocked = new Blocked();

            $this->chatServiceMock = new ChatService(
                $this->messageRoom,
                $this->messageImages,
                $this->messages,
                $this->user,
                $this->registerGift,
                $this->blocked,
            );

            $this->fileServiceMock = new FileService();

            $this->chatController = new ChatController(
                $this->app->instance(ChatService::class, $this->chatServiceMock),
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
    public function testChatSendMessage()
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

        $merchandises = $this->merchandises->create([
            'name' => 'unittest',
            'category_id' => 1,
            'giver_id' => $giver->id,
        ]);

        $merchandiseId = $merchandises->id;

        $request = new ChatRequest();
        $request->message = 'unittest';
        $request->receiver_id = $receiver->id;
        $request->merchandise_id = $merchandiseId;

        $credentials = [
            'email' => $giver->email,
            'password' => 123123
        ];

        Auth::attempt($credentials);

        $res = $this->chatController->sendMessage($request);

        $this->assertEquals(200, $res->original['status']);

        DB::rollBack();
    }

    /**
     * @author Nampx
     */
    public function testChatMessageUnread()
    {
        DB::beginTransaction();

        $fakeUser = $this->user->select('email')->first();

        $credentials = [
            'email' => $fakeUser->email,
            'password' => 123123
        ];

        Auth::attempt($credentials);

        $res = $this->chatController->messageUnread();

        $this->assertEquals(200, $res->original['status']);

        DB::rollBack();
    }

    /**
     * @author Nampx
     */
    public function testChatRoomChat()
    {
        DB::beginTransaction();

        $user = $this->user->orderBy('id', 'desc')->first();
        if (!$user) {
            return false;
        }
        $userId = $user->id;

        $fakeUser = $this->user->select('email')->first();

        $credentials = [
            'email' => $fakeUser->email,
            'password' => 123123
        ];

        Auth::attempt($credentials);

        $res = $this->chatController->roomChat($userId);

        $this->assertEquals(200, $res->original['status']);

        DB::rollBack();
    }

    /**
     * @author Nampx
     */
    public function testChatListChat()
    {
        DB::beginTransaction();

        $fakeUser = $this->user->select('email')->first();

        $credentials = [
            'email' => $fakeUser->email,
            'password' => 123123
        ];

        Auth::attempt($credentials);

        $res = $this->chatController->listChat();

        $this->assertEquals(200, $res->original['status']);

        DB::rollBack();
    }
}
