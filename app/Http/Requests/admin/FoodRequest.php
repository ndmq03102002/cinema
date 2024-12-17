<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use app\Enums\Constant;

class FoodRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true; // Giả sử tất cả người dùng đều có quyền gửi yêu cầu này
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $method = $this->method(); // Lấy phương thức HTTP (POST, PUT)

        switch ($method) {
            case 'POST': // Xử lý khi thêm mới món ăn
                return [
                    'name' => 'required|string|max:255|unique:ci_foods,name', // Bắt buộc, duy nhất trong bảng foods
                    'price' => 'required|integer|min:0', // Bắt buộc, là số nguyên không nhỏ hơn 0
                    'description' => 'nullable|string|max:255', // Có thể bỏ trống và không quá 255 ký tự
                    'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Hình ảnh tùy chọn, dung lượng không quá 2MB
                ];
            case 'PUT': // Xử lý khi cập nhật món ăn
                return [
                    'name' => 'required|string|max:255|unique:ci_foods,name,' .$this->id.'', // Bỏ qua kiểm tra unique cho món ăn hiện tại
                    'price' => 'required|integer|min:0', // Bắt buộc, là số nguyên không nhỏ hơn 0
                    'description' => 'nullable|string|max:255', // Có thể bỏ trống và không quá 255 ký tự
                    'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Hình ảnh tùy chọn, dung lượng không quá 2MB
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
            'name.required' => 'Tên món ăn là bắt buộc.',
            'name.unique' => 'Tên món ăn đã tồn tại.',
            'price.required' => 'Giá món ăn là bắt buộc.',
            'price.integer' => 'Giá món ăn phải là số nguyên.',
            'price.min' => 'Giá món ăn không được nhỏ hơn 0.',
            'description.max' => 'Mô tả không được vượt quá 255 ký tự.',
            'image.image' => 'File tải lên phải là hình ảnh.',
            'image.mimes' => 'Hình ảnh phải có định dạng jpeg, png, jpg, gif, svg.',
            'image.max' => 'Hình ảnh không được vượt quá 2MB.',
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param \Illuminate\Contracts\Validation\Validator $validator
     * @return void
     *
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
