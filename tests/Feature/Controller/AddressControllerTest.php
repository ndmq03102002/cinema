<?php

namespace Tests\Feature\Controller;

use App\Http\Controllers\Api\AddressController;
use App\Models\District;
use App\Models\Province;
use App\Models\User;
use App\Models\Ward;
use App\Services\AddressService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AddressControllerTest extends TestCase
{
    public function setUp() : void
    {
        $this->afterApplicationCreated(function () {
            $this->ward = new Ward();
            $this->district = new District();
            $this->province = new Province();
            $this->user = new User();

            $this->addressServiceMock = new AddressService(
                $this->ward,
                $this->district,
                $this->province,
            );

            $this->addressController = new AddressController(
                $this->app->instance(AddressService::class, $this->addressServiceMock)
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
    public function testProvince()
    {
        DB::beginTransaction();

        $request = new Request();
        $request->keyword = 'a';

        $res = $this->addressController->province($request);

        $this->assertEquals(200, $res->original['status']);

        DB::rollBack();
    }

    /**
     * @author Nampx
     */
    public function testDistrict()
    {
        DB::beginTransaction();

        $province = $this->province->first();
        if (!$province) {
            return false;
        }
        $provinceCode = $province->code;

        $request = new Request();
        $request->keyword = 'a';
        $request->province_code = $provinceCode;

        $res = $this->addressController->province($request);

        $this->assertEquals(200, $res->original['status']);

        DB::rollBack();
    }

    /**
     * @author Nampx
     */
    public function testWard()
    {
        DB::beginTransaction();

        $district = $this->district->first();
        if (!$district) {
            return false;
        }
        $districtCode = $district->code;

        $request = new Request();
        $request->keyword = 'a';
        $request->district_code = $districtCode;

        $res = $this->addressController->province($request);

        $this->assertEquals(200, $res->original['status']);

        DB::rollBack();
    }
}
