<?php

namespace App\Http\Requests\admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use app\Enums\Constant;
class SeatTypeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $method = $this->method();

        switch ($method) {
            case 'POST': // Xử lý khi thêm mới (create)
                return [
                    'name' => 'required|string|max:255|unique:ci_seat_type,name', // Bắt buộc, duy nhất trong bảng ci_rateds
                    'description' => 'nullable|string|max:255', // Có thể bỏ trống và không quá 255 ký tự
                    'extra_fee' => 'required|numeric|min:0', // Bắt buộc, kiểu số, không âm
                ];
            case 'PUT': // Xử lý khi cập nhật (update)
                return [
                    'name' => 'required|string|max:255|unique:ci_seat_type,name,'.$this->id.'', // Bỏ qua kiểm tra unique cho chính xếp hạng hiện tại
                    'description' => 'nullable|string|max:255', // Có thể bỏ trống và không quá 255 ký tự
                    'extra_fee' => 'required|numeric|min:0', // Bắt buộc, kiểu số, không âm
                ];
            default:
                return []; // Trả về mảng rỗng nếu không xác định được hành động
        }
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages(): array
    {
            return [
                'name.required' => 'Tên loại ghế là bắt buộc.',
                'name.unique' => 'Tên loại ghế đã tồn tại.',
                'description.max' => 'Mô tả không được vượt quá 255 ký tự.',
                'extra_fee.required' => 'Phí thêm là bắt buộc.',
                'extra_fee.numeric' => 'Phí thêm phải là số.',
                'extra_fee.min' => 'Phí thêm không được âm.',
            ]; 
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $errors = (new ValidationException($validator))->errors();

        if ($this->expectsJson()) {
            // Trả về JSON nếu request là API
            throw new HttpResponseException(response()->json([
                'statusCode' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'message' => $errors,
            ], Constant::SUCCESS_CODE));
        }

        // Trả về redirect nếu request là từ form web
        throw new HttpResponseException(redirect()
            ->back()
            ->withErrors($errors)
            ->withInput());
    }
}
