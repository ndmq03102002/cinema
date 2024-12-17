<?php

namespace App\Http\Controllers\Api\admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Movie;
use App\Models\MovieShowTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Response;
use App\Enums\Constant;
use App\Http\Requests\admin\CountryRequest;
use App\Models\Movie_Genre;

/**
 * @OA\Schema(
 *     schema="country",
 *     type="object",
 *     required={"id", "name", "description"},
 *     @OA\Property(property="id", type="integer", format="int64", example=1),
 *     @OA\Property(property="name", type="string", example="Việt Nam"),
 *     @OA\Property(property="description", type="string", example="Đất nước yêu hòa bình"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-10-09T12:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-10-09T12:00:00Z")
 * )
 */
class CountryController extends Controller
{
    public function countryIndex(){
        $countries = Country::all();
        return view('admin.country.index',compact('countries'));
    }
    public function countryCreate(){
        $config['method'] = 'create';
        return view('admin.country.create',compact('config'));
    }
    public function countryStore(CountryRequest $request)
    {
        try {
            DB::beginTransaction();
            $data = $request->all();
            Country::create($data);
            DB::commit();
            return redirect()
                ->route('country.create')
                ->with('success', trans('messages.success.success'));
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()
                ->route('country.create')
                ->with('error', $th->getMessage());
        }
        
    }

    public function countryEdit(string $id){
        $country = Country::find($id);
        $config['method'] = 'edit';
        return view('admin.country.create', compact('config','country'));
    }
  
    public function countryUpdate($id, CountryRequest $request){
        try {
            DB::beginTransaction();
            $data = $request->all();
            $query = Country::find($id);
            $query->update($data);
            DB::commit();
            return redirect()
            ->route('country.index')
            ->with('success', trans('messages.success.success'));

        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()
            ->route('country.index')
            ->with('error', $th->getMessage());
        }
    }
    public function countryDestroy($id)
    {
        // Lấy danh sách phim liên quan đến quốc gia
        $movies = Movie::where('country_id', $id)->get();

        foreach ($movies as $movie) {
            // Xóa các bản ghi trong bảng ci_movie_show_time liên kết với movie_id
            MovieShowTime::where('movie_id', $movie->id)->delete();

            // Xóa các liên kết trong bảng movie_genre
            Movie_Genre::where('movie_id', $movie->id)->delete();
           
            // Xóa phim
            $movie->delete();
        }

        // Xóa quốc gia
        Country::find($id)->delete();

        return redirect()->back()->with('success', 'Xóa country thành công.');
    }


    /**
     * @author quynhndmq
     * @OA\Get(
     *     path="/api/admin/countries",
     *     tags={"Admin Countries"},
     *     summary="Get all countries",
     *     operationId="getcountries",
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/country")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="An error occurred.")
     *         )
     *     )
     * )
     */
    public function index()
    {
        try {
            $countries = Country::all();
            if(isset($countries) && count($countries) == 0) {
                return response()->json([
                    'status' => Constant::FALSE_CODE,
                    'message' => 'No country found',
                    'data' => []
                ], Constant::SUCCESS_CODE);
            }
            return response()->json([
                'status' => Constant::SUCCESS_CODE,
                'message' => trans('messages.success.success'),
                'data' => $countries
            ], Constant::SUCCESS_CODE);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => Constant::FALSE_CODE,
                'message' => $th->getMessage(),
                'data' => []
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @author quynhndmq
     * @OA\Post(
     *     path="/api/admin/countries",
     *     tags={"Admin Countries"},
     *     summary="Create a new country",
     *     operationId="createcountry",
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Examples(
     *                 example="CreatecountryExample",
     *                 summary="Sample country creation data",
     *                 value={
     *                     "name": "Việt Nam",
     *                     "description": "Đất nước yêu hòa bình"
     *                 }
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="country created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="country created successfully."),
     *             @OA\Property(property="data", ref="#/components/schemas/country")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The name has already been taken.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="An error occurred.")
     *         )
     *     )
     * )
     */
    public function store(CountryRequest $request)
    {
        try {
            DB::beginTransaction();

            $country = Country::create($request->all());

            DB::commit();

            return response()->json([
                'status' => Constant::SUCCESS_CODE,
                'message' => 'country created successfully',
                'data' => $country
            ], Constant::SUCCESS_CODE);

        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'status' => Constant::FALSE_CODE,
                'message' => $th->getMessage(),
                'data' => []
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @author quynhndmq
     * @OA\Get(
     *     path="/api/admin/countries/{id}",
     *     tags={"Admin Countries"},
     *     summary="Get a country by ID",
     *     operationId="getcountryById",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of country",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(ref="#/components/schemas/country")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="country not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="country not found.")
     *         )
     *     )
     * )
     */
    public function show($id)
    {
        $country = Country::find($id);
        if (!$country) {
            return response()->json([
                'status' => Constant::FALSE_CODE,
                'message' => 'country not found',
                'data' => []
            ], Constant::SUCCESS_CODE);
        }
        // Route Model Binding nên không cần find('id') mà truyền thẳng object vào hàm
        return response()->json([
            'status' => Constant::SUCCESS_CODE,
            'message' => 'country retrieved successfully',
            'data' => $country
        ]);
    }

    /**
     * @author quynhndmq
     * @OA\Put(
     *     path="/api/admin/countries/{id}",
     *     tags={"Admin Countries"},
     *     summary="Update a country",
     *     operationId="updatecountry",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of country to update",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Examples(
     *                 example="UpdatecountryExample",
     *                 summary="Sample country update data",
     *                 value={
     *                     "name": "Mỹ",
     *                     "description": "Đẩt nước phim bom tấn"
     *                 }
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="country updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="country updated successfully."),
     *             @OA\Property(property="data", ref="#/components/schemas/country")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The name has already been taken.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="An error occurred.")
     *         )
     *     )
     * )
     */
    public function update($id, CountryRequest $request)
    {
        try {
            DB::beginTransaction();
            $country = Country::find($id);
            if (!$country) {
                return response()->json([
                    'status' => Constant::FALSE_CODE,
                    'message' => 'country not found',
                    'data' => []
                ], Constant::SUCCESS_CODE);
            }
            $data = $request->all();
            $country->update($data);
            DB::commit();

            return response()->json([
                'status' => Constant::SUCCESS_CODE,
                'message' => 'country updated successfully',
                'data' => $country
            ]);

        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'status' => Constant::FALSE_CODE,
                'message' => $th->getMessage(),
                'data' => []
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @author quynhndmq
     * @OA\Delete(
     *     path="/api/admin/countries/{id}",
     *     tags={"Admin Countries"},
     *     summary="Delete a country",
     *     operationId="deletecountry",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of country to delete",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="country deleted successfully",
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="An error occurred.")
     *         )
     *     )
     * )
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $movies = Movie::where('country_id', $id)->get();

         foreach ($movies as $movie) {
            // Xóa các bản ghi trong bảng ci_movie_show_time liên kết với movie_id
             MovieShowTime::where('movie_id', $movie->id)->delete();
             // Xóa các liên kết trong bảng movie_genre
             Movie_Genre::where('movie_id', $movie->id)->delete();
             
             // Xóa phim
             $movie->delete();
         }
            $country = Country::findOrFail($id);
            if (!$country) {
                return response()->json([
                    'status' => Constant::FALSE_CODE,
                    'message' => 'country not found',
                    'data' => []
                ], Constant::SUCCESS_CODE);
            }
            $country->delete();

            DB::commit();

            return response()->json([
                'status' => Constant::SUCCESS_CODE,
                'message' => 'country deleted successfully',
                'data' => []
            ], Constant::SUCCESS_CODE); // Sử dụng 200 OK hoặc 202 Accepted
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'status' => Constant::FALSE_CODE,
                'message' => $th->getMessage(),
                'data' => []
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
