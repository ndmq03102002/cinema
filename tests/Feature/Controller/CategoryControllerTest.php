<?php

namespace Tests\Feature\Controller;

use App\Http\Controllers\Api\CategoryController;
use App\Models\Category;
use App\Models\User;
use App\Services\CategoryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class CategoryControllerTest extends TestCase
{
    public function setUp() : void
    {
        $this->afterApplicationCreated(function () {
            $this->category = new Category();
            $this->user = new User();

            $this->categoryServiceMock = new CategoryService(
                $this->category,
            );

            $this->categoryController = new CategoryController(
                $this->app->instance(CategoryService::class, $this->categoryServiceMock)
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
    public function testCategoryIndex()
    {
        DB::beginTransaction();

        $res = $this->categoryController->__invoke();

        $this->assertEquals(200, $res->original['status']);

        DB::rollBack();
    }
}
