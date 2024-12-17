<?php

namespace App\Http\Controllers\Api\app;

use App\Enums\Constant;
use App\Http\Requests\app\AuthRequest;
use App\Models\Account;
use App\Models\PersonalAccessToken;
use App\Repositories\user\Account\AccountInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Models\PasswordReset;
use Illuminate\Support\Facades\Mail;
use App\Mail\ForgotPassword;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;



class AuthController extends Controller
{

    private $accountInterface;

    public function __construct(AccountInterface $accountInterface)
    {
        $this->accountInterface = $accountInterface;
    }
    /**
     * @author son.nk
     * @OA\Post (
     *     path="/api/app/auth/register",
     *     tags={"App Tài khoản"},
     *     summary="Đăng ký",
     *     operationId="app/users/create",
     *     @OA\RequestBody(
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="email", type="string"),
     *              @OA\Property(property="name", type="string"),
     *              @OA\Property(property="phone_number", type="string"),
     *              @OA\Property(property="username", type="string"),
     *              @OA\Property(property="password", type="string"),
     *          @OA\Examples(
     *              summary="Examples",
     *              example = "Examples",
     *              value = {
     *                  "email": "user1@gmail.com",
     *                  "name": "Nguyen Van A",
     *                  "phone_number": "0123456789",
     *                  "username": "user1",
     *                  "password": "123123@",
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
    public function register(AuthRequest $request){
        try {
            
            DB::beginTransaction();
            $data = $request->all();
            $this->accountInterface->register($data);
            DB::commit();
            return response()->json([
                'status' => Constant::SUCCESS_CODE,
                'message' => trans('messages.success.success'),
                'data' => []
            ], Constant::SUCCESS_CODE);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => Constant::FALSE_CODE,
                'message' => $th->getMessage(),
                'data' => []
            ], Constant::INTERNAL_SV_ERROR_CODE);
        }
    }

        /**
     * @author Sonnk
     * @OA\Post (
     *     path="/api/app/auth/login",
     *     tags={"App Tài khoản"},
     *     summary="Đăng nhập",
     *     operationId="app/users/login",
     *     @OA\RequestBody(
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="username", type="string"),
     *              @OA\Property(property="password", type="string"),
     *              @OA\Property(property="device_token", type="string"),
     *          @OA\Examples(
     *              summary="Examples",
     *              example = "Examples",
     *              value = {
     *                  "username": "user1",
     *                  "password": "123123@",
     *                  "device_token": "xxx111xxx",
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
    public function login(AuthRequest $request)
    {
        try {
            $user = $this->accountInterface->login($request);
            if (!$user) {
                return response()->json([
                    'status' => Constant::FALSE_CODE,
                    'errorCode' => 'E_UC2_1',
                    'message' => trans('messages.errors.users.email_not_found'),
                    'data' => []
                ], Response::HTTP_OK);
            }
            if ($user->status == Account::NOT_ACTIVE) {
                return response()->json([
                    'status' => Constant::FALSE_CODE,
                    'errorCode' => 'E_UC2_2',
                    'message' => trans('messages.errors.users.account_not_active'),
                    'data' => []
                ], Response::HTTP_OK);
            }

            $credentials = [
                'username' => $request->username,
                'password' => $request->password
            ];
            if (!Auth::attempt($credentials)) {
                
                return response()->json([
                    'status' => Constant::FALSE_CODE,
                    'errorCode' => 'E_UC2_3',
                    'message' => trans('messages.errors.users.password_not_correct'),
                    'data' => []
                ], Response::HTTP_OK);
            }


            // xoa token cu
            $user->tokens()->delete();

            $user->update([
                'device_token' => $request->device_token
            ]);
            $data = [
                'token' => $user->createToken($request->device_token)->plainTextToken,
                'user' => $user
            ];

            return response()->json([
                'status' => Response::HTTP_OK,
                'message' => trans('messages.success.users.login_success'),
                'data' => $data
            ], Response::HTTP_OK);

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
     *     path="/api/app/forgot-password",
     *     tags={"App Tài khoản"},
     *     summary="Request a password reset email",
     *     operationId="checkForgotPassword",
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             required={"email"},
     *             @OA\Property(property="email", type="string", format="email", description="The email of the user requesting password reset"),
     *             @OA\Examples(
     *                 example="ForgotPasswordExample",
     *                 summary="Sample forgot password data",
     *                 value={
     *                     "email": "user@example.com"
     *                 }
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Reset password email sent successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="We have sent an email, please check your inbox to reset your password.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation or token not expired",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Please check your email inbox first.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="An error occurred. Please try again later.")
     *         )
     *     )
     * )
     */

    public function checkForgotPassword(Request $request)
{
    $validator = Validator::make($request->all(), [
        'email' => 'required|email|exists:ci_account,email'
    ],[
        'email.required' => 'Email là bắt buộc.',
        'email.email' => 'Email không đúng định dạng.',
        'email.exists' => 'Email không tồn tại.'
    ]);

    if ($validator->fails()) {
        $errors = (new ValidationException($validator))->errors();
        throw new HttpResponseException(response()->json([
            'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
            'message' => $errors,
            'data' => []
        ], Constant::SUCCESS_CODE)); // Giả sử SUCCESS_CODE = 200
    }

    // Tìm tài khoản người dùng dựa trên email
    $user = Account::where('email', $request->email)->firstOrFail();

    // Tạo mã OTP ngẫu nhiên
    $rand = Str::random(40);
    $token = $rand . "email=" . $request->email;

    // Kiểm tra xem có token nào đã tồn tại chưa và xem token đó có hết hạn không
    $checkToken = PasswordReset::where('email', $request->email)->first();

    // Nếu có token và chưa hết hạn (thời gian hết hạn là 5 phút)
    $expirationTime = now()->subMinutes(5); // Thời gian hết hạn là 5 phút trước
    if ($checkToken && $checkToken->created_at > $expirationTime) {
        // Trả về lỗi nếu token chưa hết hạn
        return response()->json([
            'status' => Constant::FALSE_CODE,
            'message' => 'Vui lòng check email trước.'
        ], Constant::SUCCESS_CODE);
    } else {
        // Xóa token cũ nếu có
        PasswordReset::where('email', $request->email)->delete();
    }

    // Tạo dữ liệu token mới để lưu vào bảng password_resets
    $tokenData = [
        'email' => $request->email,
        'token' => Hash::make($token) // Mã hóa token trước khi lưu
    ];

    // Tạo bản ghi mới trong bảng password_resets
    if (PasswordReset::create($tokenData)) {
        // Gửi email với token đến người dùng
        Mail::to($request->email)->send(new ForgotPassword($user, $token));

        // Trả về thông báo thành công
        return response()->json([
            'status' => Constant::SUCCESS_CODE,
            'message' => 'Chúng tôi đã gửi email, hãy xác nhận để đổi mật khẩu.'
        ], 200);
    }

    // Nếu gặp lỗi khi tạo token
    return response()->json([
        'status' => Constant::FALSE_CODE,
        'message' => 'Lỗi! Vui lòng thử lại.'
    ], Constant::INTERNAL_SV_ERROR_CODE);
}

    public function getTokenUser(Request $request)
    {
        $user = Auth::user();
        if(!$user){
            return response()->json([
                'status' => Constant::FALSE_CODE,
                'message' => 'Token không hợp lệ',
                'data' => []
            ], Constant::SUCCESS_CODE);
        }
        return response()->json([
            'status' => Constant::SUCCESS_CODE,
            'message' => 'Success',
            'data' => $user
        ], Constant::SUCCESS_CODE);
    }
}

///**
// * @author Sonnk
// * @OA\Get (
// *     path="/api/app/bill/get-list-bill",
// *     tags={"APP Đơn hàng"},
// *     summary="Danh sách đơn hàng",
// *     security={{"bearerAuth":{}}},
// *     operationId="get-list-bill",
// *     @OA\Response(
// *         response=200,
// *         description="Success",
// *             @OA\JsonContent(
// *              @OA\Property(property="message", type="string", example="Success."),
// *          )
// *     ),
// * )
// */
