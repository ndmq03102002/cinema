<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\SpecialDay;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Response;
use App\Enums\Constant;
use App\Http\Requests\Admin\SpecialDayRequest;

// /**
//  * @OA\Schema(
//  *     schema="specialday",
//  *     type="object",
//  *     required={"id", "day_type", "description", "special_day", "extra_fee"},
//  *     @OA\Property(property="id", type="integer", format="int64", example=1),
//  *     @OA\Property(property="day_type", type="string", example="Holiday"),
//  *     @OA\Property(property="description", type="string", example="New Year's Day"),
//  *     @OA\Property(property="special_day", type="string", format="date", example="2024-01-01"),
//  *     @OA\Property(property="extra_fee", type="integer", example=10000),
//  *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-10-09T12:00:00Z"),
//  *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-10-09T12:00:00Z")
//  * )
//  */
// class SpecialDayController extends Controller
// {
//     public function specialdayIndex(){
//         $specialdays = SpecialDay::all();
//         return view('admin.specialday.index',compact('specialdays'));
//     }
//     public function specialdayCreate(){
//         $config['method'] = 'create';
//         return view('admin.specialday.create',compact('config'));
//     }
//     public function specialdayStore(SpecialDayRequest $request)
//     {
//         try {
//             DB::beginTransaction();
//             $data = $request->all();
//             SpecialDay::create($data);
//             DB::commit();
//             return redirect()
//                 ->route('specialday.create')
//                 ->with('success', trans('messages.success.success'));
//         } catch (\Throwable $th) {
//             DB::rollBack();
//             return redirect()
//                 ->route('specialday.create')
//                 ->with('error', $th->getMessage());
//         }
        
//     }

//     public function specialdayEdit(string $id){
//         $specialday = SpecialDay::find($id);
//         $config['method'] = 'edit';
//         return view('admin.specialday.create', compact('config','specialday'));
//     }
  
//     public function specialdayUpdate($id, SpecialDayRequest $request){
//         try {
//             DB::beginTransaction();
//             $data = $request->all();
//             $query = SpecialDay::find($id);
//             $query->update($data);
//             DB::commit();
//             return redirect()
//             ->route('specialday.index')
//             ->with('success', trans('messages.success.success'));

//         } catch (\Throwable $th) {
//             DB::rollBack();
//             return redirect()
//             ->route('specialday.index')
//             ->with('error', $th->getMessage());
//         }
//     }
//     public function specialdayDestroy(string $id){
//         SpecialDay::find($id)->delete();
//         return redirect()->back()->with('success', 'Xóa specialday thành công.');
//     }

//     /**
//      * @OA\Get(
//      *     path="/api/admin/specialdays",
//      *     tags={"Admin SpecialDays"},
//      *     summary="Get all special days",
//      *     operationId="getSpecialDays",
//      *     @OA\Response(
//      *         response=200,
//      *         description="Success",
//      *         @OA\JsonContent(
//      *             type="array",
//      *             @OA\Items(ref="#/components/schemas/specialday")
//      *         )
//      *     ),
//      *     @OA\Response(
//      *         response=500,
//      *         description="Internal Server Error",
//      *         @OA\JsonContent(
//      *             @OA\Property(property="message", type="string", example="An error occurred.")
//      *         )
//      *     )
//      * )
//      */
//     public function index()
//     {
//         $specialDays = SpecialDay::all();
//         return response()->json($specialDays);
//     }

//     /**
//      * @OA\Post(
//      *     path="/api/admin/specialdays",
//      *     tags={"Admin SpecialDays"},
//      *     summary="Create a new special day",
//      *     operationId="createSpecialDay",
//      *     @OA\RequestBody(
//      *         @OA\JsonContent(
//      *             type="object",
//      *             @OA\Property(property="day_type", type="string"),
//      *             @OA\Property(property="description", type="string"),
//      *             @OA\Property(property="special_day", type="string", format="date"),
//      *             @OA\Property(property="extra_fee", type="integer"),
//      *             @OA\Examples(
//      *                 example="CreateSpecialDayExample",
//      *                 summary="Sample special day creation data",
//      *                 value={
//      *                     "day_type": "holiday", 
//      *                     "description": "New Year's Day",
//      *                     "special_day": "2024-01-01",
//      *                     "extra_fee": 10000
//      *                 }
//      *             )
//      *         )
//      *     ),
//      *     @OA\Response(
//      *         response=201,
//      *         description="Special day created successfully",
//      *         @OA\JsonContent(
//      *             @OA\Property(property="status", type="string", example="success"),
//      *             @OA\Property(property="message", type="string", example="Special day created successfully."),
//      *             @OA\Property(property="data", ref="#/components/schemas/specialday")
//      *         )
//      *     ),
//      *     @OA\Response(
//      *         response=400,
//      *         description="Validation Error",
//      *         @OA\JsonContent(
//      *             @OA\Property(property="message", type="string", example="Validation error.")
//      *         )
//      *     ),
//      *     @OA\Response(
//      *         response=500,
//      *         description="Internal Server Error",
//      *         @OA\JsonContent(
//      *             @OA\Property(property="message", type="string", example="An error occurred.")
//      *         )
//      *     )
//      * )
//      */
//     public function store(SpecialDayRequest $request)
//     {
//         try {
//             DB::beginTransaction();

//             $specialDay = SpecialDay::create($request->all());

//             DB::commit();

//             return response()->json([
//                 'status' => Constant::SUCCESS_CODE,
//                 'message' => 'Special day created successfully',
//                 'data' => $specialDay
//             ], Response::HTTP_CREATED);

//         } catch (\Throwable $th) {
//             DB::rollBack();

//             return response()->json([
//                 'status' => Constant::FALSE_CODE,
//                 'message' => $th->getMessage(),
//                 'data' => []
//             ], Response::HTTP_INTERNAL_SERVER_ERROR);
//         }
//     }

//     /**
//      * @OA\Get(
//      *     path="/api/admin/specialdays/{id}",
//      *     tags={"Admin SpecialDays"},
//      *     summary="Get a special day by ID",
//      *     operationId="getSpecialDayById",
//      *     @OA\Parameter(
//      *         name="id",
//      *         in="path",
//      *         description="ID of special day",
//      *         required=true,
//      *         @OA\Schema(type="integer")
//      *     ),
//      *     @OA\Response(
//      *         response=200,
//      *         description="Success",
//      *         @OA\JsonContent(ref="#/components/schemas/specialday")
//      *     ),
//      *     @OA\Response(
//      *         response=404,
//      *         description="Special day not found",
//      *         @OA\JsonContent(
//      *             @OA\Property(property="message", type="string", example="Special day not found.")
//      *         )
//      *     )
//      * )
//      */
//     public function show($id)
//     {
//         $specialday = SpecialDay::find($id);
//         return response()->json([
//             'status' => Constant::SUCCESS_CODE,
//             'message' => 'Special day retrieved successfully',
//             'data' => $specialday
//         ]);
//     }

//     /**
//      * @OA\Put(
//      *     path="/api/admin/specialdays/{id}",
//      *     tags={"Admin SpecialDays"},
//      *     summary="Update a special day",
//      *     operationId="updateSpecialDay",
//      *     @OA\Parameter(
//      *         name="id",
//      *         in="path",
//      *         description="ID of special day to update",
//      *         required=true,
//      *         @OA\Schema(type="integer")
//      *     ),
//      *     @OA\RequestBody(
//      *         @OA\JsonContent(
//      *             type="object",
//      *             @OA\Property(property="day_type", type="string"),
//      *             @OA\Property(property="description", type="string"),
//      *             @OA\Property(property="special_day", type="string", format="date"),
//      *             @OA\Property(property="extra_fee", type="integer")
//      *         )
//      *     ),
//      *     @OA\Response(
//      *         response=200,
//      *         description="Special day updated successfully",
//      *         @OA\JsonContent(
//      *             @OA\Property(property="status", type="string", example="success"),
//      *             @OA\Property(property="message", type="string", example="Special day updated successfully."),
//      *             @OA\Property(property="data", ref="#/components/schemas/specialday")
//      *         )
//      *     ),
//      *     @OA\Response(
//      *         response=500,
//      *         description="Internal Server Error",
//      *         @OA\JsonContent(
//      *             @OA\Property(property="message", type="string", example="An error occurred.")
//      *         )
//      *     )
//      * )
//      */
//     public function update(SpecialDayRequest $request, $id)
//     {
//         try {
//             DB::beginTransaction();
//             $specialday = SpecialDay::find($id);
//             $specialday->update($request->all());

//             DB::commit();

//             return response()->json([
//                 'status' => Constant::SUCCESS_CODE,
//                 'message' => 'Special day updated successfully',
//                 'data' => $specialday
//             ]);

//         } catch (\Throwable $th) {
//             DB::rollBack();

//             return response()->json([
//                 'status' => Constant::FALSE_CODE,
//                 'message' => $th->getMessage(),
//                 'data' => []
//             ], Response::HTTP_INTERNAL_SERVER_ERROR);
//         }
//     }

//     /**
//      * @OA\Delete(
//      *     path="/api/admin/specialdays/{id}",
//      *     tags={"Admin SpecialDays"},
//      *     summary="Delete a special day",
//      *     operationId="deleteSpecialDay",
//      *     @OA\Parameter(
//      *         name="id",
//      *         in="path",
//      *         description="ID of special day to delete",
//      *         required=true,
//      *         @OA\Schema(type="integer")
//      *     ),
//      *     @OA\Response(
//      *         response=200,
//      *         description="Special day deleted successfully",
//      *     ),
//      *     @OA\Response(
//      *         response=500,
//      *         description="Internal Server Error",
//      *         @OA\JsonContent(
//      *             @OA\Property(property="message", type="string", example="An error occurred.")
//      *         )
//      *     )
//      * )
//      */

//     public function destroy($id){
//          try {
//             DB::beginTransaction();
//             $specialday = SpecialDay::find($id);
//             $specialday->delete();

//             DB::commit();

//             return response()->json([
//                 'status' => Constant::SUCCESS_CODE,
//                 'message' => 'Special day deleted successfully',
//                 'data' => []
//             ]);
//     }   catch (\Throwable $th) {
//         DB::rollBack();
//         return response()->json([
//             'status'=> 'error',
//             'message'=> $th->getMessage(),
//             'data'=> []
//         ], Response::HTTP_INTERNAL_SERVER_ERROR);

//         }
//     }
// }