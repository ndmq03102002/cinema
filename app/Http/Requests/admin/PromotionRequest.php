<?php

namespace App\Http\Requests\admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use app\Enums\Constant;

class PromotionRequest extends FormRequest
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
                    'promo_name' => 'required|string|max:255|unique:ci_promotions,promo_name', // Bắt buộc, duy nhất trong bảng ci_genre
                    'description' => 'nullable|string|max:255', // Có thể bỏ trống và không quá 255 ký tự
                    'start_date' => 'required|date|date_format:Y-m-d',
                    'end_date' => 'required|date|date_format:Y-m-d|after_or_equal:start_date',
                    'discount' => 'required|integer|min:0',
                    'quantity' => 'required|integer|min:0',
                    'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10240',
                ];
            case 'PUT': // Xử lý khi cập nhật (update)
                return [
                    'promo_name' => 'required|string|max:255|unique:ci_promotions,promo_name,'.$this->id.'', // Bỏ qua kiểm tra unique cho chính thể loại hiện tại
                    'description' => 'nullable|string|max:255', // Có thể bỏ trống và không quá 255 ký tự,
                    'start_date' => 'required|date|date_format:Y-m-d',
                    'end_date' => 'required|date|date_format:Y-m-d|after_or_equal:start_date',
                    'discount' => 'required|integer|min:0',
                    'quantity' => 'required|integer|min:0',
                    'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10240',
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
            'promo_name.required' => 'Tên khuyến mãi là bắt buộc.',
            'promo_name.unique' => 'Tên khuyến mãi đã tồn tại.',
            'description.max' => 'Mô tả không được vượt quá 255 ký tự.',
            'start_date.required' => 'Ngày bắt đầu là bắt buộc.',
            'start_date.date' => 'Ngày bắt đầu phải có định dạng hợp lệ (Y-m-d).',
            'start_date.date_format' => 'Ngày bắt đầu phải có định dạng (Y-m-d).',
            'end_date.required' => 'Ngày kết thúc là bắt buộc.',
            'end_date.date' => 'Ngày kết thúc phải có định dạng hợp lệ (Y-m-d).',
            'end_date.date_format' => 'Ngày kết thúc phải có định dạng (Y-m-d).',
            'end_date.after' => 'Ngày kết thúc phải sau ngày bắt đầu.',
            'discount.required' => 'Giảm giá là bắt buộc.',
            'discount.integer' => 'Giảm giá phải là một số nguyên.',
            'discount.min' => 'Giảm giá phải lớn hơn hoặc bằng 0.',
            'quantity.required' => 'Số lượng là bắt buộc.',
            'quantity.integer' => 'Số lượng phải là một số nguyên.',
            'quantity.min' => 'Số lượng phải lớn hơn hoặc bằng 0.',
            'avatar.image' => 'Ảnh đại diện phải là một hình ảnh.',
            'avatar.mimes' => 'Ảnh đại diện phải có định dạng: jpeg, png, jpg, gif, svg.',
            'avatar.max' => 'Ảnh đại diện không được vượt quá 10MB.',
            

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
