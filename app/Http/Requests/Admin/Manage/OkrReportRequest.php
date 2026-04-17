<?php

namespace App\Http\Requests\Admin\Manage;

use Illuminate\Foundation\Http\FormRequest;

class OkrReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_okr' => ['required'],
            'report_date' => ['required', 'date'],
            'result_value' => ['required', 'numeric', 'min:0'],
            'detail_link' => ['required', 'string', 'max:255'],
            'report_detail' => ['required', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'id_okr.required' => 'กรุณาระบุ OKR',
            'report_date.required' => 'กรุณาระบุวันที่รายงาน',
            'report_date.date' => 'รูปแบบวันที่รายงานไม่ถูกต้อง',
            'result_value.required' => 'กรุณาระบุผลดำเนินการ',
            'result_value.numeric' => 'ผลดำเนินการต้องเป็นตัวเลข',
            'result_value.min' => 'ผลดำเนินการต้องไม่น้อยกว่า 0',
            'detail_link.required' => 'กรุณาระบุลิงก์ข้อมูลรายละเอียด',
            'detail_link.max' => 'ลิงก์ข้อมูลรายละเอียดต้องไม่เกิน 255 ตัวอักษร',
            'report_detail.required' => 'กรุณากรอกรายละเอียดรายงาน',
            'report_detail.max' => 'รายละเอียดรายงานต้องไม่เกิน 255 ตัวอักษร',
        ];
    }
}