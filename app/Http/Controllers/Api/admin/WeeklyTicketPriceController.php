<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\WeeklyTicketPrice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Response;
use App\Enums\Constant;

use App\Http\Requests\Admin\WeeklyTicketPriceRequest;

/**
 * @OA\Schema(
 *     schema="weekly_ticket_price",
 *     type="object",
 *     required={"id", "name", "description", "extra_fee"},
 *     @OA\Property(property="id", type="integer", format="int64", example=1),
 *     @OA\Property(property="name", type="string", example="Monday"),
 *     @OA\Property(property="description", type="string", example="Price for weekdays."),
 *     @OA\Property(property="extra_fee", type="integer", example=45000),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-10-09T12:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-10-09T12:00:00Z")
 * )
 */
class WeeklyTicketPriceController extends Controller
{
    public function weeklyticketpriceIndex()
    {
        $weeklyticketprices = WeeklyTicketPrice::all();
        return view('admin.weekly_ticket_prices.index', compact('weeklyticketprices'));
    }
    public function weeklyticketpriceCreate()
    {
        $config['method'] = 'create';
        return view('admin.weekly_ticket_prices.create', compact('config'));
    }
    public function weeklyticketpriceStore(WeeklyTicketPriceRequest $request)
    {
        try {
            DB::beginTransaction();
            $data = $request->all();
            WeeklyTicketPrice::create($data);
            DB::commit();
            return redirect()
                ->route('weeklyticketprice.create')
                ->with('success', trans('messages.success.success'));
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()
                ->route('weeklyticketprice.create')
                ->with('error', $th->getMessage());
        }
    }

    public function weeklyticketpriceEdit(string $id)
    {
        $weeklyticketprice = WeeklyTicketPrice::find($id);
        $config['method'] = 'edit';
        return view('admin.weekly_ticket_prices.create', compact('config', 'weeklyticketprice'));
    }

    public function weeklyticketpriceUpdate($id, WeeklyTicketPriceRequest $request)
    {
        try {
            DB::beginTransaction();
            $data = $request->all();
            $query = WeeklyTicketPrice::find($id);
            $query->update($data);
            DB::commit();
            return redirect()
                ->route('weeklyticketprice.index')
                ->with('success', trans('messages.success.success'));
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()
                ->route('weeklyticketprice.index')
                ->with('error', $th->getMessage());
        }
    }
    public function weeklyticketpriceDestroy(string $id)
    {
        WeeklyTicketPrice::find($id)->delete();
        return redirect()->back()->with('success', 'Xóa weeklyticketprice thành công.');
    }
    /**
     * @OA\Get(
     *     path="/api/admin/weekly-ticket-prices",
     *     tags={"Admin WeeklyTicketPrices"},
     *     summary="Get all weekly ticket prices",
     *     operationId="getWeeklyTicketPrices",
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/weekly_ticket_price")
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
        $weeklyTicketPrices = WeeklyTicketPrice::all();
        if ($weeklyTicketPrices->isEmpty()) {
            return response()->json([
                'status' => Constant::FALSE_CODE,
                'message' => 'Weekly ticket prices not found',
                'data' => []
            ],200);
        }
        return response()->json([
            'status' => Constant::SUCCESS_CODE,
            'message' => 'Weekly ticket prices retrieved successfully',
            'data' =>$weeklyTicketPrices
        ],200);
    }

    /**
     * @OA\Post(
     *     path="/api/admin/weekly-ticket-prices",
     *     tags={"Admin WeeklyTicketPrices"},
     *     summary="Create a new weekly ticket price",
     *     operationId="createWeeklyTicketPrice",
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="extra_fee", type="integer"),
     *             @OA\Examples(
     *                 example="CreateWeeklyTicketPriceExample",
     *                 summary="Sample weekly ticket price creation data",
     *                 value={
     *                     "name": "Monday",
     *                     "description": "Price for weekends.",
     *                     "extra_fee": 45000
     *                 }
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Weekly ticket price created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Weekly ticket price created successfully."),
     *             @OA\Property(property="data", ref="#/components/schemas/weekly_ticket_price")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Validation error.")
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
    public function store(WeeklyTicketPriceRequest $request)
    {
        try {
            DB::beginTransaction();

            $weeklyTicketPrice = WeeklyTicketPrice::create($request->all());

            DB::commit();

            return response()->json([
                'status' => Constant::SUCCESS_CODE,
                'message' => 'Weekly ticket price created successfully',
                'data' => $weeklyTicketPrice
            ], 200);
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
     * @OA\Get(
     *     path="/api/admin/weekly-ticket-prices/{id}",
     *     tags={"Admin WeeklyTicketPrices"},
     *     summary="Get a weekly ticket price by ID",
     *     operationId="getWeeklyTicketPriceById",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of weekly ticket price",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(ref="#/components/schemas/weekly_ticket_price")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Weekly ticket price not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Weekly ticket price not found.")
     *         )
     *     )
     * )
     */
    public function show($id)
    {
        $weekly_ticket_price = WeeklyTicketPrice::find($id);
        if (!$weekly_ticket_price) {
            return response()->json([
                'status' => Constant::FALSE_CODE,
                'message' => 'Weekly ticket price not found',
                'data' => []
            ], 200);
        }
        return response()->json([
            'status' => Constant::SUCCESS_CODE,
            'message' => 'Weekly ticket price retrieved successfully',
            'data' => $weekly_ticket_price
        ]);
    }

    /**
     * @OA\Put(
     *     path="/api/admin/weekly-ticket-prices/{id}",
     *     tags={"Admin WeeklyTicketPrices"},
     *     summary="Update a weekly ticket price",
     *     operationId="updateWeeklyTicketPrice",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of weekly ticket price to update",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="extra_fee", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Weekly ticket price updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Weekly ticket price updated successfully."),
     *             @OA\Property(property="data", ref="#/components/schemas/weekly_ticket_price")
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
    public function update(WeeklyTicketPriceRequest $request, $id)
    {
        try {
            DB::beginTransaction();
            $weekly_ticket_price = WeeklyTicketPrice::find($id);
            if (!$weekly_ticket_price) {
                return response()->json([
                    'status' => Constant::FALSE_CODE,
                    'message' => 'Weekly ticket price not found',
                    'data' => []
                ], 200);
            }
            $weekly_ticket_price->update($request->all());

            DB::commit();

            return response()->json([
                'status' => Constant::SUCCESS_CODE,
                'message' => 'Weekly ticket price updated successfully',
                'data' => $weekly_ticket_price
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
     * @OA\Delete(
     *     path="/api/admin/weekly-ticket-prices/{id}",
     *     tags={"Admin WeeklyTicketPrices"},
     *     summary="Delete a weekly ticket price",
     *     operationId="deleteWeeklyTicketPrice",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of weekly ticket price to delete",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Weekly ticket price deleted successfully",
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
            $weekly_ticket_price = WeeklyTicketPrice::find($id);
            if (!$weekly_ticket_price) {
                return response()->json([
                    'status' => Constant::FALSE_CODE,
                    'message' => 'Weekly ticket price not found',
                    'data' => []
                ], 200);
            }
            $weekly_ticket_price->delete();

            DB::commit();

            return response()->json([
                'status' => Constant::SUCCESS_CODE,
                'message' => 'Weekly ticket price deleted successfully',
                'data' => []
            ], Response::HTTP_OK);
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
