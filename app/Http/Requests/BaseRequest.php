<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

abstract class BaseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    abstract public function rules() : array;

    public function messages()
    {
        return [
            'required' => 'Vui lòng nhập :attribute.',
            'email' => 'Email không hợp lệ.',
            'unique' => ':Attribute đã tồn tại.',
            'exists' => ':Attribute không tồn tại.',
            'max' => ':Attribute quá dài, tối đa :max ký tự.',
            'min' => ':Attribute quá ngắn, tối thiểu :min ký tự.',
            'confirmed' => ':Attribute xác nhận không chính xác.',
            'file.max' => ':Attribute quá nặng, tối đa :max KB.',
            'mimes' => ':Attribute không hợp lệ, định dạng hợp lệ: :values'
        ];
    }
}
