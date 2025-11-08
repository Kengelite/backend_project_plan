<?php

namespace App\Http\Requests\Admin\Manage;

use Illuminate\Foundation\Http\FormRequest;

class OkrRequest extends FormRequest
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
            'okr_number' => 'required|string|max:255',
            'okr_name' => 'required|string|max:255',
            'goal' => 'required|int',
            'result' => 'required|numeric',
            'report_data' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'id_unit' => 'required|int',
            'id_year' => 'required|int',
            'employee' => 'required|array',
            'employee.*.id' => 'required|string',
            'employee.*.name' => 'required|string',
            'teacher' => 'required|array',
            'teacher.*.id' => 'required|string',
            'teacher.*.name' => 'required|string',
        ];
    }

    public function messages()
    {
        return [
            'okr_number.required' => 'กรุณากรอกเลข OKR',
            'okr_number.string'   => 'เลข OKR ต้องเป็นข้อความ',
            'okr_number.max'      => 'เลข OKR ต้องไม่เกิน 255 ตัวอักษร',

            'okr_name.required' => 'กรุณากรอกชื่อ OKR',
            'okr_name.string'   => 'ชื่อ OKR ต้องเป็นข้อความ',
            'okr_name.max'      => 'ชื่อ OKR ต้องไม่เกิน 255 ตัวอักษร',

            'goal.required' => 'กรุณากรอกเป้าหมาย',
            'goal.int'      => 'เป้าหมายต้องเป็นตัวเลข',

            'result.required' => 'กรุณากรอกผลลัพธ์',
            'result.numeric' => 'ผลลัพธ์ต้องเป็นทศนิยม',

            'report_data.required' => 'กรุณากรอกข้อมูลรายงาน',
            'report_data.string'   => 'ข้อมูลรายงานต้องเป็นข้อความ',

            'start_date.required' => 'กรุณากรอกวันที่เริ่มต้น',
            'start_date.date'     => 'วันที่เริ่มต้นต้องอยู่ในรูปแบบวันที่',

            'end_date.required' => 'กรุณากรอกวันที่สิ้นสุด',
            'end_date.date'     => 'วันที่สิ้นสุดต้องอยู่ในรูปแบบวันที่',

            'id_unit.required' => 'กรุณาเลือกหน่วย',
            'id_unit.int'      => 'ผลลัพธ์ต้องเป็นตัวเลข',

            'id_year.required' => 'กรุณาเลือกปีงบประมาณ',
            'id_year.int'      => 'ผลลัพธ์ต้องเป็นตัวเลข',


            'employee.required' => 'กรุณาระบุผู้รับผิดชอบ',
            'employee.array' => 'ผู้รับผิดชอบต้องเป็นรายการ',
            'employee.*.id.required' => 'กรุณาระบุ ID ของผู้รับผิดชอบ',
            'employee.*.name.required' => 'กรุณาระบุชื่อของผู้รับผิดชอบ',

            'teacher.required' => 'กรุณาระบุอาจารย์ที่เกี่ยวข้อง',
            'teacher.array' => 'อาจารย์ต้องเป็นรายการ',
            'teacher.*.id.required' => 'กรุณาระบุ ID ของอาจารย์',
            'teacher.*.name.required' => 'กรุณาระบุชื่อของอาจารย์',
        ];
    }
}
