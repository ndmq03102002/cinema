<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use app\Enums\Constant;
class TimeSlotRequest extends FormRequest
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
            case 'POST': // Rules for creating a new time slot
                return [
                    'slot_name' => 'required|string|max:255|unique:ci_time_slots,slot_name',
                    'start_time' => 'required|date_format:H:i',
                    'end_time' => 'required|date_format:H:i|after:start_time',
                    'extra_fee' => 'required|integer|min:0',
                ];
            case 'PUT': // Rules for updating an existing time slot
                return [
                    'slot_name' => 'required|string|max:255|unique:ci_time_slots,slot_name,'.$this->id.'',
                    'start_time' => 'required|date_format:H:i',
                    'end_time' => 'required|date_format:H:i|after:start_time',
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
            'slot_name.required' => 'Tên khung giờ là bắt buộc.',
            'slot_name.unique' => 'Tên khung giờ đã tồn tại.',
            'slot_name.max' => 'Tên khung giờ không được vượt quá 255 ký tự.',
            'start_time.required' => 'Thời gian bắt đầu là bắt buộc.',
            'start_time.date_format' => 'Thời gian bắt đầu phải có định dạng HH:MM:SS.',
            'end_time.required' => 'Thời gian kết thúc là bắt buộc.',
            'end_time.date_format' => 'Thời gian kết thúc phải có định dạng HH:MM:SS.',
            'end_time.after' => 'Thời gian kết thúc phải sau thời gian bắt đầu.',
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
