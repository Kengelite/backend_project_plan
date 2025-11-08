<?php

namespace App\Services\Admin\Manage;  // คำสั่ง namespace ต้องอยู่บรรทัดแรก

use App\Dto\ActivitySpendMoneyDTO;
use App\Trait\Utils;
use App\Models\ActivitySpendmoney;
use App\Models\Project;
use App\Models\Activity;
use App\Models\ActionPlan;
use App\Models\ActivityDetail;
use App\Models\Strategic;
use App\Models\ActivityDetailSpendmoney;
use App\Models\Year;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Exceptions\CustomException;

class ActivitySpendMoneyService
{
    use Utils;

    public function getAll($perPage)
    {
        $activity = ActivitySpendmoney::paginate($perPage)->withQueryString();
        return $activity;
    }
    public function getidactivity($id)
    {
        // $activity = ActivitySpendmoney::paginate($perPage)->withQueryString();
        // return $activity;
        $activity = ActivitySpendmoney::with('unit')
            ->where('id_activity', $id)
            ->orderBy('created_at')
            ->get();
        return $activity;
    }
    public function show($id)
    {
        $data = ActivitySpendmoney::findOrFail($id);
        return $data;
    }
    public function store(ActivitySpendMoneyDTO $activityDetailDTO)
    {
        $projectDB = new ActivitySpendmoney();

        DB::transaction(function () use ($activityDetailDTO, &$projectDB) {
            // $projectDB->id_user = $id_user;
            $projectDB->id_activity = $activityDetailDTO->activityId;
            $projectDB->activity_spendmoney_name = $activityDetailDTO->name;
            $projectDB->id_unit = $activityDetailDTO->idUnit;
            $projectDB->save();
        });

        // ส่งเฉพาะ id กลับ
        return $projectDB;
    }

    public function update($id, $activityDetailDTO)
    {
        return DB::transaction(function () use ($id, $activityDetailDTO) {
            $activityDB = ActivitySpendmoney::findOrFail($id);

            // อัปเดตข้อมูลให้ตรงกับ create
            $activityDB->id_activity = $activityDetailDTO->activityId;
            $activityDB->activity_spendmoney_name = $activityDetailDTO->name;
            $activityDB->id_unit = $activityDetailDTO->idUnit;

            $activityDB->save();

            return $activityDB;
        });
    }
    public function delete($id)
    {
        // นับลูกว่ามีกี่ตัว
        $countDetails = ActivityDetailSpendmoney::where('id_activity_spendmoney', $id)
            ->whereNull('deleted_at')
            ->count();

        if ($countDetails > 0) {
            // ให้ controller จับแล้วส่ง 409 กลับ
            throw new CustomException(
                "ไม่สามารถลบได้ เนื่องจากยังมีการใช้งานอยู่ ({$countDetails} รายการ)",
                409
            );
        }

        // ถ้าไม่มีลูก → ลบแม่ได้
        $activity = ActivitySpendmoney::where('activity_spendmoney_id', $id)->firstOrFail();
        $activity->delete(); // SoftDelete

        return [
            'message' => 'deleted',
            'id'      => $activity,
        ];
    }



    // public function delete($id)
    // {
    //     $result = [];

    //     $deleted = DB::transaction(function () use ($id, &$result) {
    //         $activityDetail = ActivitySpendmoney::where('activity_spendmoney_id', $id)->firstOrFail();
    //         $total = ActivityDetailSpendmoney::whereNull('deleted_at')
    //             ->where('id_activity_spendmoney', $id)
    //             ->select(DB::raw('SUM(price * amount) as total'))
    //             ->value('total');
    //         $priceToSubtract = floatval($total);


    //         $activity_detail = ActivityDetailSpendmoney::whereNull('deleted_at')
    //             ->where('id_activity_spendmoney', $id)
    //             ->select('id_activity_detail', DB::raw('SUM(price * amount) as total'))
    //             ->groupBy('id_activity_detail')
    //             ->get();


    //         // // 1. อัปเดต Activity
    //         foreach ($activity_detail as $item) {
    //             $activity = ActivityDetail::findOrFail($item->id_activity_detail);
    //             $activity->price -= floatval($item->total);
    //             $activity->save();
    //             $result[$activity->detail] = floatval($activity->price);
    //         }

    //         // 1. อัปเดต Activity
    //         $activity = Activity::findOrFail($activityDetail->id_activity);
    //         $activity->spend_money -= $priceToSubtract;
    //         $activity->save();
    //         $result['activity_remain_budget'] = floatval($activity->budget) - floatval($activity->spend_money);


    //         // 2. อัปเดต Project
    //         $project = Project::findOrFail($activity->id_project);
    //         $project->spend_money -= $priceToSubtract;
    //         $project->save();
    //         $result['project_remain_budget'] = floatval($project->budget) - floatval($project->spend_money);

    //         // 3. อัปเดต ActionPlan
    //         $actionplan = ActionPlan::findOrFail($project->id_action_plan);
    //         $actionplan->spend_money -= $priceToSubtract;
    //         $actionplan->save();
    //         $result['actionplan_remain_budget'] = floatval($actionplan->budget) - floatval($actionplan->spend_money);

    //         // 4. อัปเดต Strategic
    //         $strategic = Strategic::findOrFail($actionplan->id_strategic);
    //         $strategic->spend_money -= $priceToSubtract;
    //         $strategic->save();
    //         $result['strategic_remain_budget'] = floatval($strategic->budget) - floatval($strategic->spend_money);

    //         // 5. ลบ ActivityDetail
    //         $activityDetail->id_employee = Auth::id();
    //         $activityDetail->delete();

    //         return $activityDetail;
    //     });

    //     return response()->json([
    //         'deleted_activity_detail' => $deleted,
    //         'remain_budget_summary' => $result
    //     ]);
    // }
}
