<?php

namespace App\Http\Requests\admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use app\Enums\Constant;
class CinemaRequest extends FormRequest
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
        $action = $this->segments()[3];

        switch ($action):
            case ($action == 'create'):
                $rule = [
                    'name' => 'required|unique:ci_cinema,name',
                    'address' => 'required',
                    'avatar' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:10240',

                ];
                break;
            case ($action == 'update'):
                $rule = [
                    'name' => 'required|unique:ci_cinema,name,'.$this->id.'',
                    'address' => 'required',
                    'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10240',
                ];
                break;
            case ($action == 'delete' || $action == 'get'):
                $rule = [
                    'id' => 'required|exists:ci_cinema,id',
                ];
                break;
        endswitch;

        return $rule;
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'name.unique' => 'Rạp phim đã tồn tại',
            'address.required' => 'Địa chỉ rạp không được bỏ trống',
            'name.required' => 'Tên rạp không được bỏ trống',
            'id.required' => 'Id không được bỏ trống',
            'id.exists' => 'Id không tồn tại',
            'avatar.required' => 'Ảnh đại diện không được bỏ trống',
            'avatar.image' => 'Ảnh đại diện phải là ảnh',
            'avatar.mimes' => 'Ảnh đại diện phải có định dạng jpeg,png,jpg,gif,svg',
            'avatar.max' => 'Ảnh đại diện không được vượt quá 10MB',
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
