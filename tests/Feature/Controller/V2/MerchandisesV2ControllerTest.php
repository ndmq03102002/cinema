<?php

namespace Tests\Feature\Controller\V2;

use App\Enums\Constant;
use App\Http\Controllers\Api\V2\MerchandisesV2Controller;
use App\Http\Requests\V2\MerchandisesV2Request;
use App\Models\Category;
use App\Models\MerchandiseImages;
use App\Models\Merchandises;
use App\Models\Transaction;
use App\Models\User;
use App\Services\FileUploadServices\FileService;
use App\Services\V2\MerchandisesV2Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class MerchandisesV2ControllerTest extends TestCase
{
    public function setUp() : void
    {
        $this->afterApplicationCreated(function () {
            $this->merchandise = new Merchandises();
            $this->category = new Category();
            $this->user = new User();
            $this->merchandiseImages = new MerchandiseImages();
            $this->transaction = new Transaction();

            $this->merchandiseV2ServiceMock = new MerchandisesV2Service(
                $this->merchandise,
                $this->transaction,
                $this->merchandiseImages
            );

            $this->fileServiceMock = new FileService();

            $this->merchandiseV2Controller = new MerchandisesV2Controller(
                $this->app->instance(MerchandisesV2Service::class, $this->merchandiseV2ServiceMock),
                $this->app->instance(Merchandises::class, $this->merchandise),
                $this->app->instance(MerchandiseImages::class, $this->merchandiseImages),
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
     * @return void
     * @author Nampx
     */
    public function testMerchandiseV2Index()
    {
        DB::beginTransaction();

        $request = new Request();
        $request->keyword = 'sản phẩm check đánh giá';
        $request->status = 0;
        $request->name = 'nampx';
        $request->perpage = 10;

        $res = $this->merchandiseV2Controller->index($request);

        $this->assertEquals(200, $res->original['status']);

        DB::rollBack();
    }

    /**
     * @return void
     * @author Nampx
     */
    public function testMerchandiseV2Show()
    {
        DB::beginTransaction();

        $merchandise = $this->merchandise->first();
        if (!$merchandise) {
            return false;
        }
        $id = $merchandise->id;

        $res = $this->merchandiseV2Controller->show($id);

        $this->assertEquals(200, $res->original['status']);

        DB::rollBack();
    }

    /**
     * @return void
     * @author Nampx
     */
    public function testMerchandiseV2Update()
    {
        DB::beginTransaction();

        $merchandise = $this->merchandise->first();
        if (!$merchandise) {
            return false;
        }
        $merchandiseId = $merchandise->id;

        $request = new MerchandisesV2Request();
        $request->name = 'unittest';
        $request->image_delete = json_encode([]);
        $request->image_data = [new \Illuminate\Http\UploadedFile(resource_path('unittest.jpg'), 'unittest.jpg', null, null, true)];

        $res = $this->merchandiseV2Controller->update($request, $merchandiseId);

        $this->assertEquals(200, $res->original['status']);

        DB::rollBack();
    }

    /**
     * @return void
     * @author Nampx
     */
    public function testMerchandiseV2Destroy()
    {
        DB::beginTransaction();

        $merchandise = $this->merchandise->first();
        if (!$merchandise) {
            return false;
        }
        $merchandiseId = $merchandise->id;

        $res = $this->merchandiseV2Controller->destroy($merchandiseId);

        $this->assertEquals(200, $res->original['status']);

        DB::rollBack();
    }
}
