<?php

namespace App\Http\Requests\Admin\Manage;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'role' => ['required'],
            'url_img' => ['required', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
            'academic_position' => ['nullable', 'string', 'max:255'],
            'id_position' => ['required'],
        ];

        if ($this->isMethod('post')) {
            $rules['password'] = ['required', 'string', 'min:8',];
            $rules['url_img'] = ['required', 'image', 'mimes:jpeg,png,jpg', 'max:2048'];
        } else {
            $rules['password'] = ['nullable', 'string', 'min:8'];
            $rules['url_img'] = ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'];

        }

        return $rules;
    }


    public function messages()
    {
        return [
            'name.required' => 'กรุณากรอกชื่อ',
            'email.required' => 'กรุณากรอกอีเมล',
            'email.email' => 'รูปแบบอีเมลไม่ถูกต้อง',
            'password.required' => 'กรุณากรอกรหัสผ่าน',
            'password.min' => 'รหัสผ่านต้องมีอย่างน้อย 8 ตัวอักษร',
            'role.required' => 'กรุณาเลือกบทบาท',
            'url_img.image' => 'ไฟล์ต้องเป็นรูปภาพเท่านั้น',
            'url_img.required' => 'กรุณาอัพรูป',
            'id_position.required' => 'กรุณาระบุตำแหน่ง',
        ];
    }
}
