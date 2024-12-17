<?php

namespace Tests\Feature\Controller;

use App\Enums\Constant;
use App\Http\Controllers\Api\MerchandisesController;
use App\Http\Requests\MerchandisesRequest;
use App\Models\Blocked;
use App\Models\Category;
use App\Models\District;
use App\Models\MerchandiseImages;
use App\Models\MerchandiseRating;
use App\Models\Merchandises;
use App\Models\MessageImages;
use App\Models\MessageRoom;
use App\Models\Messages;
use App\Models\Province;
use App\Models\RegisterGift;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Ward;
use App\Services\ChatService;
use App\Services\FileUploadServices\FileService;
use App\Services\MerchandisesService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class MerchandisesControllerTest extends TestCase
{
    public function setUp() : void
    {
        $this->afterApplicationCreated(function () {
            $this->merchandises = new Merchandises();
            $this->merchandiseImages = new MerchandiseImages();
            $this->merchandiseRating = new MerchandiseRating();
            $this->transaction = new Transaction();
            $this->category = new Category();
            $this->province = new Province();
            $this->district = new District();
            $this->ward = new Ward();
            $this->messageRoom = new MessageRoom();
            $this->messageImages = new MessageImages();
            $this->messages = new Messages();
            $this->registerGift = new RegisterGift();
            $this->user = new User();
            $this->blocked = new Blocked();

            $this->merchandiseServiceMock = new MerchandisesService(
                $this->merchandises,
                $this->merchandiseImages,
                $this->merchandiseRating,
                $this->category,
                $this->transaction,
            );

            $this->chatServiceMock = new ChatService(
                $this->messageRoom,
                $this->messageImages,
                $this->messages,
                $this->user,
                $this->registerGift,
                $this->blocked,
            );

            $this->fileServiceMock = new FileService();

            $this->merchandisesController = new MerchandisesController(
                $this->app->instance(Merchandises::class, $this->merchandises),
                $this->app->instance(ChatService::class, $this->chatServiceMock),
                $this->app->instance(User::class, $this->user),
                $this->app->instance(MerchandisesService::class, $this->merchandiseServiceMock),
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
    public function testMerchandiseIndex()
    {
        DB::beginTransaction();

        $request = new Request();

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

        $res = $this->merchandisesController->index($request);

        $this->assertEquals(200, $res->original['status']);

        DB::rollBack();
    }

    /**
     * @author Nampx
     */
    public function testMerchandiseStore()
    {
        DB::beginTransaction();

        $category = $this->category->first();
        if (!$category) {
            return false;
        }
        $categoryId = $category->id;

        $province = $this->province->first();
        if (!$province) {
            return false;
        }
        $provinceId = $province->code;

        $district = $this->district->first();
        if (!$district) {
            return false;
        }
        $districtId = $district->code;

        $ward = $this->ward->first();
        if (!$ward) {
            return false;
        }
        $wardId = $ward->code;

        $request = new MerchandisesRequest();
        $request->name = 'unittest_name';
        $request->description = 'unittest_description';
        $request->category_id = $categoryId;
        $request->condition = 'unittest_condition';
        $request->amount = 100;
        $request->given_date = '02-02-2022';
        $request->province_given_code = $provinceId;
        $request->district_given_code = $districtId;
        $request->ward_given_code = $wardId;
        $request->image_data = [new \Illuminate\Http\UploadedFile(resource_path('unittest.jpg'), 'unittest.jpg', null, null, true)];


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

        $res = $this->merchandisesController->store($request);

        $this->assertEquals(200, $res->original['status']);

        DB::rollBack();
    }

    /**
     * @author Nampx
     */
    public function testMerchandiseShow()
    {
        DB::beginTransaction();

        $merchandises = $this->merchandises->first();
        if (!$merchandises) {
            return false;
        }
        $merchandiseId = $merchandises->id;

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

        $res = $this->merchandisesController->show($merchandiseId);

        $this->assertEquals(200, $res->original['status']);

        DB::rollBack();
    }

    /**
     * @author Nampx
     */
    public function testMerchandiseUpdate()
    {
        DB::beginTransaction();

        $category = $this->category->first();
        if (!$category) {
            return false;
        }
        $categoryId = $category->id;

        $province = $this->province->first();
        if (!$province) {
            return false;
        }
        $provinceId = $province->code;

        $district = $this->district->first();
        if (!$district) {
            return false;
        }
        $districtId = $district->code;

        $ward = $this->ward->first();
        if (!$ward) {
            return false;
        }
        $wardId = $ward->code;

        $request = new MerchandisesRequest();
        $request->name = 'unittest_name2';
        $request->description = 'unittest_description2';
        $request->category_id = $categoryId;
        $request->condition = 'unittest_condition';
        $request->amount = 100;
        $request->given_date = '02-02-2022';
        $request->province_given_code = $provinceId;
        $request->district_given_code = $districtId;
        $request->ward_given_code = $wardId;
        $request->image_delete = json_encode([]);
        $request->image_data = [new \Illuminate\Http\UploadedFile(resource_path('unittest.jpg'), 'unittest.jpg', null, null, true)];


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


        $res = $this->merchandisesController->update($request, $merchandiseId);

        $this->assertEquals(200, $res->original['status']);

        DB::rollBack();
    }

    /**
     * @author Nampx
     */
    public function testMerchandiseSearchData()
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

        $res = $this->merchandisesController->searchData();

        $this->assertEquals(200, $res->original['status']);

        DB::rollBack();
    }

    /**
     * @author Nampx
     */
    public function testMerchandiseDelSearchData()
    {
        DB::beginTransaction();

        $request = new Request();
        $request->keyword = 'a';

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

        $res = $this->merchandisesController->delSearchData($request);

        $this->assertEquals(200, $res->original['status']);

        DB::rollBack();
    }
}
