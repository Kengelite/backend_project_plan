<?php

namespace App\Http\Requests\Admin\Manage;

use Illuminate\Foundation\Http\FormRequest;

class ActivityDetailSpendMoneyRequest extends FormRequest
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
            'id_activity_detail' => 'required|string|max:255',
            'id_activity_spendmoney' => 'required|string|max:255',
            'price' => 'required|numeric|between:0,99999999999.99',
            'amount' => 'required|numeric|between:0,99999999999.99',
        ];
    }

    public function messages(): array
    {
        return [
            // id_activity_detail
            'id_activity_detail.required' => 'กรุณาระบุรหัสรายละเอียดกิจกรรม',
            'id_activity_detail.string' => 'รหัสรายละเอียดกิจกรรมต้องเป็นข้อความ',
            'id_activity_detail.max' => 'รหัสรายละเอียดกิจกรรมต้องไม่เกิน 255 ตัวอักษร',

            // id_activity_spendmoney
            'id_activity_spendmoney.required' => 'กรุณาระบุรหัสรายการใช้จ่าย',
            'id_activity_spendmoney.string' => 'รหัสรายการใช้จ่ายต้องเป็นข้อความ',
            'id_activity_spendmoney.max' => 'รหัสรายการใช้จ่ายต้องไม่เกิน 255 ตัวอักษร',

            // price
            'price.required' => 'กรุณากรอกราคา',
            'price.numeric' => 'ราคาต้องเป็นตัวเลข',
            'price.between' => 'ราคาต้องอยู่ระหว่าง 0 ถึง 99,999,999,999.99',

            // amount
            'amount.required' => 'กรุณากรอกจำนวน',
            'amount.numeric' => 'จำนวนต้องเป็นตัวเลข',
            'amount.between' => 'จำนวนต้องอยู่ระหว่าง 0 ถึง 99,999,999,999.99',
        ];
    }
}
