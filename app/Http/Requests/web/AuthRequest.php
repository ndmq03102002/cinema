<?php
//
//namespace App\Http\Requests\web;
//
//use App\Models\Users;
//use Illuminate\Foundation\Http\FormRequest;
//use Illuminate\Http\Exceptions\HttpResponseException;
//use Illuminate\Validation\ValidationException;
//use Symfony\Component\HttpFoundation\Response;
//use Illuminate\Validation\Rule;
//
//class AuthRequest extends FormRequest
//{
//    /**
//     * Determine if the user is authorized to make this request.
//     *
//     * @return bool
//     */
//    public function authorize(): bool
//    {
//        return true;
//    }
//
//    /**
//     * Get the validation rules that apply to the request.
//     *
//     * @return array<string, mixed>
//     */
//    public function rules()
//    {
//        $action = $this->segments()[3];
//        switch ($action):
//            case($action == 'login'):
//                $rule = [
//                    'email' => 'required|',
//                    'password' => 'required|min:6|max:55',
//                    'device_token' => 'nullable',
//                ];
//                break;
//            case($action == 'register'):
//                $rule = [
//                    'email' => 'required|email|unique:users,email',
//                    'full_name' => 'required|',
//                    'phone_number'=> 'required|min:10|max:11|unique:users_info',
//                    'address' => 'required',
//                    'password' => 'required|min:6|max:55',
//                    'device_token' => 'nullable',
//                ];
//                break;
//            case($action == 'forgot-password'):
//                $rule = [
//                    'email' => 'required|exists:users',
//                ];
//                break;
//            case($action == 'resend-code'):
//                $rule = [
//                    'email' => 'required|exists:users'
//                ];
//                break;
//            case($action == 'verify-code'):
//                $rule = [
//                    'email' => 'required|exists:users',
//                    'code' => 'required',
//                ];
//                break;
//            case($action == 'reset-password'):
//                $rule = [
//                    'email' => 'required|exists:users',
//                    'code' => 'required',
//                    'password' => 'required|min:6|max:55'
//                ];
//                break;
//            case($action == 'edit-profile'):
//                $rule = [
//                    'full_name' => 'required|',
//                    'phone_number' => 'required|min:10|max:11',
//                    'address' => 'required|',
//                    'avatar' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
//
//                ];
//                break;
//            case($action == 'change-password'):
//                $rule = [
//                    'old_password' => 'required',
//                    'new_password' => 'required|regex:/(^([A-Za-z0-9]{6,50}+)?$)/u|confirmed',
//                ];
//                break;
//            default:
//                $rule = [];
//        endswitch;
//
//        return $rule;
//    }
//
//    /**
//     * Get the error messages for the defined validation rules.
//     *
//     * @return array
//     */
//    public function messages(): array
//    {
//        return [
//            'email.unique' => 'Email đã tồn tại',
//            'email.email' => 'Email sai định dạng',
//            'email.exists' => 'Email không tồn tại',
//            'email.required' => 'Email là bắt buộc',
//            'email.prohibited' => 'Email không tồn tại',
//            'code.required' => 'Mã OTP là bắt buộc',
//            'password.required' => 'Mật khẩu là trường bắt buộc',
//            'password.regex' => 'Mật khẩu không chính xác',
//            'password.min' => 'Mật khẩu có độ dài tối thiểu là 6 kí tự',
//            'password.max' => 'Mật khẩu có độ dài tối đa là 55 kí tự',
//            'name.required' => "Họ và tên là bắt buộc",
//            'phone_number.unique' => "Số điện thoại đã tồn tại",
//            'phone_number.required' => "Số điện thoại là bắt buộc",
//            'phone_number.min' => "Số điện thoại có độ dài không phù hợp",
//            'phone_number.max' => "Số điện thoại có độ dài không phù hợp",
//            'new_password.required' => 'Mật khẩu mới là trường bắt buộc',
//            'new_password.confirmed' => 'Mật khẩu mới không trùng nhau',
//            'old_password.required' => 'Mật khẩu cũ là trường bắt buộc',
//            'new_password.regex' => 'Mật khẩu mới sai định dạng',
//            'address.required' => 'Địa chỉ không được bỏ trống',
//            'avatar.mimes' => 'Ảnh không đúng định dạng',
//            'avatar.max' => 'Kích thước ảnh phải nhỏ hơn 2Mb',
//        ];
//    }
//
//    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
//    {
//        $errors = (new ValidationException($validator))->errors();
//        throw new HttpResponseException(response()->json([
//            'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
//            'message' => $errors,
//            'data' => []
//        ], Response::HTTP_UNPROCESSABLE_ENTITY));
//    }
//}
