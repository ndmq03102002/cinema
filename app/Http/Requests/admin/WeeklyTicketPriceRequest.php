<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use app\Enums\Constant;
class WeeklyTicketPriceRequest extends FormRequest
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
            case 'POST': // Rules for creating a new weekly ticket price
                return [
                    'name' => 'required|string|max:255|unique:ci_weekly_ticket_prices,name',
                    'description' => 'nullable|string|max:500',
                    'extra_fee' => 'required|integer|min:0',
                ];
            case 'PUT': // Rules for updating an existing weekly ticket price
                return [
                    'name' => 'required|string|max:255|unique:ci_weekly_ticket_prices,name,'.$this->id.'',
                    'description' => 'nullable|string|max:500',
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
            'name.required' => 'Tên giá vé là bắt buộc.',
            'name.unique' => 'Tên giá vé đã tồn tại.',
            'name.max' => 'Tên giá vé không được vượt quá 255 ký tự.',
            'description.max' => 'Mô tả không được vượt quá 500 ký tự.',
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
            ], Constant::SUCCESS_CODE));
        }

        // Trả về redirect nếu request là từ form web
        throw new HttpResponseException(redirect()
            ->back()
            ->withErrors($errors)
            ->withInput());
    }
}
