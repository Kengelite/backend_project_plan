<?php


namespace App\Services\Admin\Manage;

use App\Trait\Utils;
use App\Models\roundFileUpload;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Activity;
use App\Models\FileDataUpload;
use Illuminate\Http\Exceptions\HttpResponseException;

class RoundFileUploadService
{

    use Utils;
    public function getAll($perPage)
    {
        return RoundFileUpload::with('year')
            ->withSum('filedataupload', 'money')
            ->orderByDesc('id_year')   // เรียงปีใหม่ → เก่า
            ->orderBy('round')     // แล้วเรียงรอบในปีนั้น
            ->paginate($perPage)
            ->withQueryString();
    }




    public function uploaddata($data, $file, $id_year)
    {
        DB::beginTransaction();

        try {

            // 1️⃣ บันทึกชื่อไฟล์ก่อน (ได้ id_file กลับมา)
            $lastRound = RoundFileUpload::where('id_year', $id_year)
                ->max('round');   // หาค่าสูงสุดของรอบในปีนั้น

            $newRound = $lastRound ? $lastRound + 1 : 1; // ถ้ายังไม่มี ให้เริ่มที่ 1

            $roundFile = RoundFileUpload::create([
                'file_name' => $file,
                'id_year'   => $id_year,
                'round'     => $newRound,
                'id_user'     => Auth::id(),   // ✅ ดึงจากผู้ใช้งานที่ล็อกอินอยู่

            ]);

            $id_file = $roundFile->file_id; // ใช้เป็น FK ต่อ

            $results = [];

            foreach ($data as $row) {

                // 2️⃣ หา activity ที่ตรงกับเงื่อนไข
                $activity = Activity::select('activity_id', 'budget', 'actual_money', 'name_activity')
                    ->where('id_year', $id_year)
                    ->where('id', $row['A'])   // เปลี่ยนจาก id เป็น number

                    ->whereHas('project', function ($q) use ($id_year, $row) {
                        $q->where('id_year', $id_year)
                            ->where('project_number', $row['P'])

                            ->whereHas('actionplan', function ($q2) use ($id_year, $row) {
                                $q2->where('id_year', $id_year)
                                    ->where('action_plan_number', $row['AC'])

                                    ->whereHas('strategic', function ($q3) use ($id_year, $row) {
                                        $q3->where('id_year', $id_year)
                                            ->where('strategic_number', $row['CP']);
                                    });
                            });
                    })
                    ->first();

                // ถ้าไม่เจอ activity ให้หยุดทั้งหมด
                if (!$activity) {
                    throw new \Exception("ไม่พบ Activity สำหรับ CP={$row['CP']} AC={$row['AC']} P={$row['P']} A={$row['A']}");
                }

                $newMoney = (float) $row['money'];
                $budget = (float) $activity->budget;

                // ✅ เช็กแค่ว่ายอดใหม่เกินงบไหม (ไม่สนยอดเก่า)
                if ($newMoney > $budget) {
                    throw new \App\Exceptions\CustomException(
                        "ยอดเงินเกินงบประมาณ",
                        [
                            'activity' => $activity->name_activity,
                            'budget' => $budget,
                            'request_money' => $newMoney
                        ],
                        400
                    );
                }


                // 4️⃣ บันทึกลง file_data_upload
                FileDataUpload::create([
                    'id_file'     => $id_file,
                    'id_activity' => $activity->activity_id,
                    'money'       => $newMoney,

                ]);
                // 5️⃣ อัปเดต actual_money ใน activity (บวกเพิ่ม)
                $activity->actual_money = $newMoney;
                $activity->save();
                $results[] = [
                    'activity_id' => $activity->activity_id,
                    'budget' => $budget,
                    'new_actual' => $activity->actual_money
                ];
            }

            DB::commit();
            return $results;
        } catch (\Exception $e) {

            DB::rollBack();

            // \Log::error("UPLOAD ERROR: " . $e->getMessage(), [
            //     'trace' => $e->getTraceAsString()
            // ]);

            return response()->json([
                'message' => $e->getMessage() ?: "เกิดข้อผิดพลาดภายในระบบ",
                'error_code' => 'UPLOAD_FAILED'
            ], 400);
        }
    }



    // public function uploaddata($data, $file, $id_year)
    // {
    //     $activities = Activity::select('activity_id', 'budget')
    //         ->where('id_year', 1)   // 🔹 ชั้น Activity

    //         ->whereHas('project', function ($q) use ($id_year) {
    //             $q->where('id_year', 1)     // 🔹 ชั้น Project
    //                 ->where('project_number', 2)

    //                 ->whereHas('actionplan', function ($q2) use ($id_year) {
    //                     $q2->where('id_year', 1)  // 🔹 ชั้น ActionPlan
    //                         ->where('action_plan_number', 1)
    //                         ->whereHas('strategic', function ($q3) use ($id_year) {
    //                             $q3->where('id_year', 1)  // 🔹 ชั้น Strategic
    //                                 ->where('strategic_number', 'CP5');
    //                         });
    //                 });
    //         })

    //         ->where('id', 1)   // activity = 1
    //         ->where('id_year', 1)
    //         ->get();
    //     return $activities;
    // }
}
