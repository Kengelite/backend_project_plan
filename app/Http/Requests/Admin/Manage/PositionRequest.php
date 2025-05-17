<?php

namespace App\Http\Requests\Admin\Manage;

use Illuminate\Foundation\Http\FormRequest;

class PositionRequest extends FormRequest
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
            'position_name' => 'required|string|max:255',
        ];
    }

    public function messages()
    {
        return [
            'position_name.required' => 'กรุณากรอกชื่อตำแหน่ง',
            'position_name.string' => 'ชื่อตำแหน่งต้องเป็นข้อความ',
            'position_name.max' => 'ชื่อตำแหน่งต้องไม่เกิน 255 ตัวอักษร',
        ];
    }
}
