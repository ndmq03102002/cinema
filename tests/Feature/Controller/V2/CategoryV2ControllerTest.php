<?php

namespace Tests\Feature\Controller\V2;

use App\Http\Controllers\Api\V2\CategoryV2Controller;
use App\Http\Requests\V2\CategoryV2Request;
use App\Models\Category;
use App\Services\FileUploadServices\FileService;
use App\Services\V2\CategoryV2Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class CategoryV2ControllerTest extends TestCase
{
    public function setUp() : void
    {
        $this->afterApplicationCreated(function () {
            $this->category = new Category();

            $this->categoryV2ServiceMock = new CategoryV2Service(
                $this->category,
            );

            $this->fileServiceMock = new FileService();

            $this->categoryV2Controller = new CategoryV2Controller(
                $this->app->instance(CategoryV2Service::class, $this->categoryV2ServiceMock),
                $this->app->instance(Category::class, $this->category),
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
    public function testCategoryV2Index()
    {
        DB::beginTransaction();

        $request = new Request();
        $request->keyword = 'a';
        $request->perpage = 10;

        $res = $this->categoryV2Controller->index($request);

        $this->assertEquals(200, $res->original['status']);

        DB::rollBack();
    }

    /**
     * @return void
     * @author Nampx
     */
    public function testCategoryV2Store()
    {
        DB::beginTransaction();

        $request = new CategoryV2Request();
        $request->display_name = 'unittest';
        $request->image_data = new \Illuminate\Http\UploadedFile(resource_path('unittest.jpg'), 'unittest.jpg', null, null, true);

        $res = $this->categoryV2Controller->store($request);

        $this->assertEquals(200, $res->original['status']);

        DB::rollBack();
    }

    /**
     * @return void
     * @author Nampx
     */
    public function testCategoryV2Show()
    {
        DB::beginTransaction();

        $category = $this->category->first();
        if (!$category) {
            return false;
        }
        $categoryId = $category->id;

        $res = $this->categoryV2Controller->show($categoryId);

        $this->assertEquals(200, $res->original['status']);

        DB::rollBack();
    }

    /**
     * @return void
     * @author Nampx
     */
    public function testCategoryV2Update()
    {
        DB::beginTransaction();

        $request = new CategoryV2Request();
        $request->display_name = 'unittest2';
        $request->iamge_delete = 1;
        $request->image_data = new \Illuminate\Http\UploadedFile(resource_path('unittest.jpg'), 'unittest.jpg', null, null, true);

        $category = $this->category->first();
        if (!$category) {
            return false;
        }
        $categoryId = $category->id;

        $res = $this->categoryV2Controller->update($request, $categoryId);

        $this->assertEquals(200, $res->original['status']);

        DB::rollBack();
    }

    /**
     * @return void
     * @author Nampx
     */
    public function testCategoryV2Destroy()
    {
        DB::beginTransaction();

        $category = $this->category->first();
        if (!$category) {
            return false;
        }
        $categoryId = $category->id;

        $res = $this->categoryV2Controller->destroy($categoryId);

        $this->assertEquals(200, $res->original['status']);

        DB::rollBack();
    }
}
