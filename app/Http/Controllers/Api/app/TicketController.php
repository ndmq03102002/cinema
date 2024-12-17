<?php

namespace App\Http\Controllers\Api\app;

use App\Enums\Constant;
use App\Helpers\CommonHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\admin\CinemaRequest;
use App\Models\Account;
use App\Models\Cinema;
use App\Models\Ticket;
use App\Models\Bill;
use App\Models\Movie;
use App\Models\Room;
use App\Models\Seat;
use App\Models\Profile;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Symfony\Component\HttpFoundation\Response;
use App\Repositories\user\Ticket\TicketRepository;
use App\Models\Promotion;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;

class TicketController extends Controller
{
    private $endpoint;
    private $partnerCode;
    private $accessKey;
    private $orderInfo;
    private $requestType;
    private $redirectUrl;
    private $ipnUrl;
    private $secretKey;
    public $ticketRepository;
    public function __construct
    (
        TicketRepository $ticketRepository,
    )
    {
        $this->ticketRepository = $ticketRepository;
        $this->endpoint = "https://test-payment.momo.vn/v2/gateway/api/create";
        $this->partnerCode = 'MOMOBKUN20180529';
        $this->accessKey = 'klm05TvNBzhg7h7j';
        $this->secretKey = 'at67qH6mk8w5Y1nAyMoYKMWACiEi2bsa';
        $this->orderInfo = "Thanh toán qua MoMo";
        $this->requestType = "captureWallet";
        $this->redirectUrl = "http://localhost:3000/cart";
        $this->ipnUrl = "http://localhost:3000/cart";
    }

    /**
     * @author son.nk
     * @OA\Post (
     *     path="/api/app/ticket/reservation",
     *     tags={"App Đặt vé"},
     *     summary="Giữ chỗ",
     *     operationId="user/ticket/reservation",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="cinema_id", type="string"),
     *              @OA\Property(property="seat_id", type="string"),
     *              @OA\Property(property="show_time_id", type="string"),
     *          @OA\Examples(
     *              summary="Examples",
     *              example = "Examples",
     *              value = {
     *                          "cinema_id" : "1",
     *                          "show_time_id" : "7",
     *                          "seat_ids" : "[5, 6, 7, 8, 9]"
     *                      },
     *              ),
     *          )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *             @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Success."),
     *          )
     *     ),
     * )
     */
    public function createReservation(Request $request){
        try {
            $data = $request->all();
            Redis::setex($data['cinema_id'] . '_' . $data['show_time_id'] . '_' . $this->getCurrentLoggedIn()->id , 600, $data['seat_ids']);
            return response()->json([
                'status' => Constant::SUCCESS_CODE,
                'message' => trans('messages.success.success'),
                'data' => []
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => Constant::FALSE_CODE,
                'message' => $th->getMessage(),
                'data' => []
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    /**
     * @author Sonnk
     * @OA\Post (
     *     path="/api/app/ticket/momo-payment",
     *     tags={"App Đặt vé"},
     *     summary="Thanh toán Momo",
     *     security={{"bearerAuth":{}}},
     *     operationId="ticket/momo-payment",
     *     @OA\Parameter(
     *          in="header",
     *          name="X-localication",
     *          required=false,
     *          description="Ngôn ngữ",
     *          @OA\Schema(
     *            type="string",
     *            example="vi",
     *          )
     *     ),
     *     @OA\RequestBody(
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="amount", type="string"),
     *              @OA\Property(property="cinema_id", type="string"),
     *              @OA\Property(property="show_time_id", type="string"),
     *              @OA\Property(property="extraData", type="string"),
     *              @OA\Property(property="food_id", type="string"),
     *              @OA\Property(property="food_quantity", type="string"),
     *          @OA\Examples(
     *              summary="Examples",
     *              example = "Examples",
     *              value = {
     *                  "amount": "10000",
     *                  "cinema_id": "1",
     *                  "show_time_id": "7",
     *                  "extraData": "1",
     *                  "food_id": "4",
     *                  "food_quantity": 4
     *                  },
     *              ),
     *          )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *             @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Success."),
     *          )
     *     ),
     * )
     */
    public function momoPayment(Request $request){
//        $this->appointmentTime = $request->appointmentTime;
        $data = $request->all();
        $amount = $data['amount'];
        $endpoint = $this->endpoint;
        $partnerCode = $this->partnerCode;
        $accessKey = $this->accessKey;
        $secretKey = $this->secretKey;
        $orderInfo = $this->orderInfo;
        $key = $data['cinema_id'] . '_' . $data['show_time_id'] . '_' . $this->getCurrentLoggedIn()->id;
        if(!Redis::get($key)){
            return response()->json([
                'status' => Constant::SUCCESS_CODE,
                'message' => trans('messages.errors.ticket.time_out'),
                'data' => []
            ], Constant::SUCCESS_CODE);
        }
        $orderId = $key . '_' . time();
        $food = [
            'food_id' => $data['food_id'],
            'food_quantity' => $data['food_quantity']
        ];
        Redis::expire($orderId, 300);
        Redis::setex('extraData_' . $this->getCurrentLoggedIn()->id . '_' . $data['cinema_id'] . '_' . $data['show_time_id'] , 300, $data['extraData']);
        Redis::setex('food_' . $this->getCurrentLoggedIn()->id . '_' . $data['cinema_id'] . '_' . $data['show_time_id'], 300, json_encode($food));
        $redirectUrl = $this->redirectUrl;
        $ipnUrl = $this->ipnUrl;
        $extraData = "";
        $requestId = time() . "";
        $requestType = $this->requestType;
        $rawHash = "accessKey=" . $accessKey . "&amount=" . $amount . "&extraData=" . $extraData . "&ipnUrl=" . $ipnUrl . "&orderId=" . $orderId . "&orderInfo=" . $orderInfo . "&partnerCode=" . $partnerCode . "&redirectUrl=" . $redirectUrl . "&requestId=" . $requestId . "&requestType=" . $requestType;
        $signature = hash_hmac("sha256", $rawHash, $secretKey);
        $data = array('partnerCode' => $partnerCode,
            'partnerName' => "Test",
            "storeId" => "MomoTestStore",
            'requestId' => $requestId,
            'amount' => $amount,
            'orderId' => $orderId,
            'orderInfo' => $orderInfo,
            'redirectUrl' => $redirectUrl,
            'ipnUrl' => $ipnUrl,
            'lang' => 'vi',
            'extraData' => $extraData,
            'requestType' => $requestType,
            'signature' => $signature,
//            'appointmentTime' => $this->appointmentTime
        );
        $result = CommonHelper::execPostRequest($endpoint, json_encode($data));
        $jsonResult = json_decode($result, true);
        return response()->json([
            'status' => Constant::SUCCESS_CODE,
            'message' => trans('messages.success.success'),
            'data' => $jsonResult
        ], Constant::SUCCESS_CODE);
    }

//    public function applyPromotionAfterPayment($id)
//    {
//        $promotion = Promotion::find($id);
//
//        if (!$promotion || $promotion->status != 1) {
//            return;
//        }
//
//        // Kiểm tra ngày áp dụng khuyến mãi
//        if ($promotion->end_date < now() || $promotion->start_date > now()) {
//            return;
//        }
//
//        // Kiểm tra số lượng khuyến mãi còn lại
//        if ($promotion->quantity <= 0) {
//            return;
//        }
//
//        $userId = Auth::id();
//
//        // Kiểm tra người dùng đã sử dụng mã khuyến mãi này chưa
//        if ($promotion->users()->where('account_id', $userId)->exists()) {
//            return;
//        }
//
//        // Ghi nhận người dùng đã sử dụng mã và giảm số lượng còn lại
//        $promotion->users()->attach($userId);
//        $promotion->decrement('quantity');
//    }


     public function handleMomoPayment(Request $request){
         $responseData = $request->all();
         $partnerCode = $this->partnerCode;
         $accessKey = $this->accessKey;
         $secretKey = $this->secretKey;
         $orderInfo = $this->orderInfo;
         $amount = $responseData["amount"];
         $orderId = $responseData['orderId'];
         $extraData =  "";
         $requestId = $responseData['requestId'];
         $signature = $responseData['signature'];
         $rawHash = "accessKey=" . $accessKey . "&amount=" . $amount . "&extraData=" . $extraData .
             "&message=" . $responseData["message"] . "&orderId=" . $orderId . "&orderInfo=" . $orderInfo .
             "&orderType=" . $responseData["orderType"] . "&partnerCode=" . $partnerCode . "&payType=" . $responseData["payType"] . "&requestId=" . $requestId .
             "&responseTime=" . $responseData["responseTime"] . "&resultCode=" . $responseData["resultCode"] . "&transId=" . $responseData["transId"];
         $generatedSignature = hash_hmac("sha256", $rawHash, $secretKey);
         if ($generatedSignature == $signature) {
             if($responseData['resultCode'] == '0') {

                 $data = $this->ticketRepository->createBill($orderId, $amount);
                 return response()->json([
                     'status' => Constant::SUCCESS_CODE,
                     'message' => trans('messages.success.success'),
                     'data' => $data
                 ], Constant::SUCCESS_CODE);
             }
             else{
                 $data = $this->ticketRepository->cancelReservation($orderId);
                 return response()->json([
                     'status' => Constant::BAD_REQUEST_CODE,
                     'message' => trans('messages.errors.errors'),
                     'data' => [1]
                 ], Constant::SUCCESS_CODE);
             }
         } else {
             $data = $this->ticketRepository->cancelReservation($orderId);
             return response()->json([
                 'status' => Constant::BAD_REQUEST_CODE,
                 'message' => trans('messages.errors.errors'),
                 'data' => [1]
             ], Constant::SUCCESS_CODE);
         }
     }


//    public function handleMomoPayment(Request $request)
//    {
//        dd('check');
//        $responseData = $request->all();
//        $partnerCode = $this->partnerCode;
//        $accessKey = $this->accessKey;
//        $secretKey = $this->secretKey;
//        $orderInfo = $this->orderInfo;
//        $amount = $responseData["amount"];
//        $orderId = $responseData['orderId'];
//        $extraData = "";
//        $requestId = $responseData['requestId'];
//        $signature = $responseData['signature'];
//        $rawHash = "accessKey=" . $accessKey . "&amount=" . $amount . "&extraData=" . $extraData .
//            "&message=" . $responseData["message"] . "&orderId=" . $orderId . "&orderInfo=" . $orderInfo .
//            "&orderType=" . $responseData["orderType"] . "&partnerCode=" . $partnerCode . "&payType=" . $responseData["payType"] . "&requestId=" . $requestId .
//            "&responseTime=" . $responseData["responseTime"] . "&resultCode=" . $responseData["resultCode"] . "&transId=" . $responseData["transId"];
//        $generatedSignature = hash_hmac("sha256", $rawHash, $secretKey);
//        if ($generatedSignature == $signature) {
//            if ($responseData['resultCode'] == '0') {
//
//                $this->ticketRepository->createBill($orderId, $amount);
//
//
//    //            if (!empty($extraData)) {
//    //                $promotionId = intval($extraData);
//    //                $this->applyPromotionAfterPayment($promotionId);
//    //            }
//
//                return response()->json([
//                    'status' => Constant::SUCCESS_CODE,
//                    'message' => trans('messages.success.success'),
//                    'data' => [0]
//                ], Constant::SUCCESS_CODE);
//            } else {
//                // Hủy vé khi thanh toán thất bại
//                $this->ticketRepository->cancelReservation($orderId);
//                return response()->json([
//                    'status' => Constant::BAD_REQUEST_CODE,
//                    'message' => trans('messages.errors.errors'),
//                    'data' => [1]
//                ], Constant::SUCCESS_CODE);
//            }
//        } else {
//            // Hủy vé khi xác thực không thành công
//            $this->ticketRepository->cancelReservation($orderId);
//            return response()->json([
//                'status' => Constant::BAD_REQUEST_CODE,
//                'message' => trans('messages.errors.errors'),
//                'data' => [1]
//            ], Constant::SUCCESS_CODE);
//        }
//    }

    /**
     * @OA\Get (
     *     path="/api/app/ticket/get/{type}",
     *     tags={"App Đặt vé"},
     *     summary="Vé đã đặt",
     *     operationId="app/ticket/get",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *          name="type",
     *          in="path",
     *          description="type (0: lấy vé đã chiếu, 1: lấy phim chưa chiếu)",
     *          required=true,
     *          @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Success."),
     *         )
     *     ),
     * )
     */
    public function getTicket($type){
        try {
            $memberId = $this->getCurrentLoggedIn()->id;
            if($memberId){
                $data = $this->ticketRepository->getBill($memberId, $type);
                return response()->json([
                    'status' => Constant::SUCCESS_CODE,
                    'message' => trans('messages.success.success'),
                    'data' => $data
                ], Constant::SUCCESS_CODE);
            }else{
                return response()->json([
                    'status' => Constant::SUCCESS_CODE,
                    'message' => trans('messages.success.required_login'),
                    'data' => []
                ], Constant::SUCCESS_CODE);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => Constant::FALSE_CODE,
                'message' => $th->getMessage(),
                'data' => []
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Get (
     *     path="/api/app/ticket/detail/{id}",
     *     tags={"App Đặt vé"},
     *     summary="Chi tiết vé",
     *     operationId="app/ticket/detail",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="id bill cần xem chi tiết",
     *          required=true,
     *          @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Success."),
     *         )
     *     ),
     * )
     */
    public function detailTicket($id){
        try {
            $memberId = $this->getCurrentLoggedIn()->id;
            if($memberId){
                $data = $this->ticketRepository->getBillDetail($id);
                return response()->json([
                    'status' => Constant::SUCCESS_CODE,
                    'message' => trans('messages.success.success'),
                    'data' => $data
                ], Constant::SUCCESS_CODE);
            }else{
                return response()->json([
                    'status' => Constant::SUCCESS_CODE,
                    'message' => trans('messages.success.required_login'),
                    'data' => []
                ], Constant::SUCCESS_CODE);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => Constant::FALSE_CODE,
                'message' => $th->getMessage(),
                'data' => []
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

     /**
     * @OA\Get(
     *     path="/api/app/get-ticket",
     *     tags={"Staff Check"},
     *     summary="Get bill detail by ticket_code",
     *     description="Retrieve detailed information about a bill using its ticket_code.",
     *     operationId="getBillDetail",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="ticket_code",
     *         in="query",
     *         description="The unique code of the bill.",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             example="HD123456"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Bill detail retrieved successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Lấy thông tin hóa đơn thành công."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="ticket_code", type="string", example="HD123456"),
     *                 @OA\Property(property="email", type="string", example="customer@example.com"),
     *                 @OA\Property(property="movie", type="string", example="Movie Name"),
     *                 @OA\Property(property="start_time", type="string", format="time", example="08:00"),
     *                 @OA\Property(property="end_time", type="string", format="time", example="10:00"),
     *                 @OA\Property(property="start_date", type="string", format="date", example="2024-12-10"),
     *                 @OA\Property(property="room", type="string", example="Room 01"),
     *                 @OA\Property(property="cinema", type="string", example="Cinema Name"),
     *                 @OA\Property(property="seats", type="array", @OA\Items(type="string"), example={"A1", "A2"}),
     *                 @OA\Property(property="status", type="integer", example=0),
     *                 @OA\Property(property="staff_check", type="integer", nullable=true, example=1)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Bill not found.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Hóa đơn không tồn tại."),
     *             @OA\Property(property="data", type="array", @OA\Items())
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(
     *                     property="ticket_code",
     *                     type="array",
     *                     @OA\Items(type="string", example="Ticket_code là bắt buộc.")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function getBillDetail(Request $request)
    {
        $request->validate([
            'ticket_code' => 'required'
        ]);

        $validator = Validator::make($request->all(), [
            'ticket_code' => 'required'
        ],[
            'ticket_code.required' => 'Ticket_code là bắt buộc.',

        ]);

        if ($validator->fails()) {
            $errors = (new ValidationException($validator))->errors();
            throw new HttpResponseException(response()->json([
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'message' => $errors,
                'data' => []
            ], Constant::SUCCESS_CODE)); // Giả sử SUCCESS_CODE = 200
        }


        $bill = Bill::with([
            'movieShowTime:id,start_time,end_time,start_date,room_id,movie_id',
            'account:id,email',
            'cinema:id,name'
        ])
        ->where('ticket_code', $request->ticket_code)->first();

        if (!$bill) {
            return response()->json([
                'status' => Constant::FALSE_CODE,
                'message' => 'Hóa đơn không tồn tại.',
                'data' => []
            ], 200);
        }

        if($bill->status==1 && $bill->staff_check!=0 ){
            $name_nv = Profile::find($bill->staff_check)->name;
            $message = 'Vé đã được duyệt bởi nhân viên '.$name_nv;
        }else{
            $message = 'Vé chưa được duyệt';
        }
        return response()->json([
            'status' => Constant::SUCCESS_CODE,
            'message' => $message,
            'data' => [
                'ticket_code' => $bill->ticket_code,
                'email' => $bill->account->email,
                'movie' => Movie::find($bill->movieShowTime->movie_id)->name,
                'start_time' => $bill->movieShowTime->start_time,
                'end_time' => $bill->movieShowTime->end_time,
                'start_date' => $bill->movieShowTime->start_date,
                'room' => Room::find($bill->movieShowTime->room_id)->name,
                'cinema' => $bill->cinema->name,
                'seats' => Seat::whereIn('id', function ($query) use ($bill) {
                    $query->select('seat_id')
                          ->from('ci_ticket')
                          ->where('bill_id', $bill->id);
                })->pluck('seat_code')->toArray(),
                'status' => $bill->status,
                'staff_check' => $bill->staff_check == 0 ? null : $name_nv,

            ]
        ], Response::HTTP_OK);
    }


    /**
     * @OA\Post(
     *     path="/api/app/check-bill",
     *     tags={"Staff Check"},
     *     summary="Check and approve a bill",
     *     description="Check and approve a bill by ticket_code for a customer.",
     *     operationId="checkBill",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="ticket_code",
     *         in="query",
     *         description="The unique code of the bill to be checked.",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             example="HD123456"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Bill checked successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Duyệt hóa đơn thành công."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="ticket_code", type="string", example="HD123456"),
     *                 @OA\Property(property="status", type="integer", example=1)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Bill not found.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Hóa đơn không tồn tại."),
     *             @OA\Property(property="data", type="array", @OA\Items())
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(
     *                     property="ticket_code",
     *                     type="array",
     *                     @OA\Items(type="string", example="Ticket_code là bắt buộc.")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bill already checked or invalid.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Hóa đơn đã được duyệt hoặc không hợp lệ."),
     *             @OA\Property(property="data", type="array", @OA\Items())
     *         )
     *     )
     * )
     */
    public function checkBill(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ticket_code' => 'required|exists:ci_bill,ticket_code'
        ], [
            'ticket_code.required' => 'Ticket_code là bắt buộc.',
            'ticket_code.exists' => 'Hóa đơn không tồn tại.'

        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => Constant::FALSE_CODE,
                'message' => 'Lỗi validation',
                'errors' => $validator->errors()
            ], 200);
        }

        $bill = Bill::where('ticket_code', $request->ticket_code)->first();

        if (!$bill) {
            return response()->json([
                'status' => Constant::FALSE_CODE,
                'message' => 'Hóa đơn không tồn tại.',
                'data' => []
            ], 200);
        }

        if ($bill->status == 1 || $bill->staff_check != 0) {
            return response()->json([
                'status' => Constant::FALSE_CODE,
                'message' => 'Hóa đơn đã được duyệt hoặc không hợp lệ.',
                'data' => []
            ], 200);
        }

        // Lấy thông tin nhân viên đang đăng nhập
        $staff = Auth::user();
        if($staff->role_id == 4){
            return response()->json([
                'status' => Constant::FALSE_CODE,
                'message' => 'Bạn không có quyền duyệt vé',
                'data' => []
            ], 200);
        }
        // Cập nhật trạng thái và id nhân viên duyệt
        $bill->status = 1;
        $bill->staff_check = $staff->id;
        $bill->save();

        return response()->json([
            'status' => Constant::SUCCESS_CODE,
            'message' => 'Duyệt hóa đơn thành công.',
            'data' => [
                'ticket_code' => $bill->ticket_code,
                'status' => $bill->status,
                'staff_check' => $bill->staff_check
            ]
        ], 200);
    }


}
