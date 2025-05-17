<?php

namespace App\Http\Requests\Admin\Manage;

use Illuminate\Foundation\Http\FormRequest;

class TypeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'style_name' => 'required|string|max:255',
        ];
    }

    public function messages()
    {
        return [
            'style_name.required' => 'กรุณากรอกชื่อประเภท',
            'style_name.string' => 'ชื่อประเภทต้องเป็นข้อความ',
            'style_name.max' => 'ชื่อประเภทต้องไม่เกิน 255 ตัวอักษร',
        ];
    }
}
