<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use app\Enums\Constant;
class SpecialDayRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true; // Bạn có thể điều chỉnh lại logic xác thực nếu cần
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
            case 'POST': // Quy tắc cho việc tạo một ngày đặc biệt mới
                return [
                    'day_type' => 'required|string|max:255',
                    'description' => 'nullable|string|max:255',
                    'special_day' => 'required|date|date_format:Y-m-d|unique:ci_special_days,special_day',
                    'extra_fee' => 'required|integer|min:0',
                ];
            case 'PUT': // Quy tắc cho việc cập nhật một ngày đặc biệt đã tồn tại
                return [
                    'day_type' => 'required|string|max:255',
                    'description' => 'nullable|string|max:255',
                    'special_day' => 'required|date|date_format:Y-m-d|unique:ci_special_days,special_day,'.$this->id.'',
                    'extra_fee' => 'required|integer|min:0',
                ];
            default:
                return [];
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
            'day_type.required' => 'Loại ngày là bắt buộc.',
            'day_type.string' => 'Loại ngày phải là một chuỗi.',
            'day_type.max' => 'Loại ngày không được vượt quá 255 ký tự.',
            'description.string' => 'Mô tả phải là một chuỗi.',
            'description.max' => 'Mô tả không được vượt quá 255 ký tự.',
            'special_day.required' => 'Ngày đặc biệt là bắt buộc.',
            'special_day.date' => 'Ngày đặc biệt phải là một ngày hợp lệ.',
            'special_day.date_format' => 'Ngày đặc biệt phải có định dạng YYYY-MM-DD.',
            'special_day.unique' => 'Ngày đặc biệt đã tồn tại.',
            'extra_fee.required' => 'Phí thêm là bắt buộc.',
            'extra_fee.integer' => 'Phí thêm phải là một số nguyên.',
            'extra_fee.min' => 'Phí thêm phải lớn hơn hoặc bằng 0.',
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param \Illuminate\Contracts\Validation\Validator $validator
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $errors = (new ValidationException($validator))->errors();

        if ($this->expectsJson()) {
            // Trả về JSON nếu request là API
            throw new HttpResponseException(response()->json([
                'statusCode' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'message' => $errors,
            ], Response::HTTP_UNPROCESSABLE_ENTITY));
        }

        // Trả về redirect nếu request là từ form web
        throw new HttpResponseException(redirect()
            ->back()
            ->withErrors($errors)
            ->withInput());
    }
}
