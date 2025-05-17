<?php

namespace App\Http\Requests\Admin\Manage;

use Illuminate\Foundation\Http\FormRequest;

class PrincipleRequest extends FormRequest
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
            'principle_name' => 'required|string|max:255',
        ];
    }

    public function messages()
    {
        return [
            'principle_name.required' => 'กรุณากรอกชื่อหลักธรรมภิบาล',
            'principle_name.string' => 'ชื่อหลักธรรมภิบาลต้องเป็นข้อความ',
            'principle_name.max' => 'ชื่อหลักธรรมภิบาลต้องไม่เกิน 255 ตัวอักษร',
        ];
    }
}
