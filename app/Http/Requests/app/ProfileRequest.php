<?php

namespace App\Http\Requests\app;

use Illuminate\Foundation\Http\FormRequest;
use app\Enums\Constant;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class ProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'age' => 'required|integer',
            'phone_number' => 'required|string|max:15',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10240',  // avatar có thể là URL hoặc đường dẫn ảnh
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'name.required' => 'Tên là bắt buộc.',
            'name.string' => 'Tên phải là một chuỗi văn bản.',
            'name.max' => 'Tên không được vượt quá 255 ký tự.',
            'age.required' => 'Tuổi là bắt buộc.',
            'age.integer' => 'Tuổi phải là một số nguyên.',
            'phone_number.required' => 'Số điện thoại là bắt buộc.',
            'phone_number.string' => 'Số điện thoại phải là một chuỗi văn bản.',
            'phone_number.max' => 'Số điện thoại không được vượt quá 15 ký tự.',
            'avatar.image' => 'Ảnh đại diện phải là một tệp hình ảnh.',
            'avatar.mimes' => 'Ảnh đại diện phải có định dạng: jpeg, png, jpg, gif, svg.',
            'avatar.max' => 'Ảnh đại diện không được vượt quá 10MB.',
        ];
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $errors = (new ValidationException($validator))->errors();
        throw new HttpResponseException(response()->json([
            'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
            'message' => $errors,
            'data' => []
        ], Constant::SUCCESS_CODE));
    }
}
