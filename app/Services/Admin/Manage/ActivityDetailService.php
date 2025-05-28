<?php

namespace App\Services\Admin\Manage;

use App\Dto\ActivityDetailDTO;
use App\Models\ActivityDetail;
use App\Models\Activity;
use App\Models\Project;
use App\Models\ActionPlan;
use App\Models\Strategic;
use App\Trait\Utils;
use Illuminate\Support\Facades\DB;

class ActivityDetailService
{
    use Utils;
    public function getAll()
    {
        $activityDetail = ActivityDetail::paginate(10)->withQueryString();
        return $activityDetail;
    }
    public function getByID($id)
    {
        $activityDetail = ActivityDetail::findOrFail($id);
        return $activityDetail;
    }
    public function getByIDactivityAdmin($id,$perPage)
    {
        $activityDetail = ActivityDetail::where('id_activity', $id)
            ->orderBy('report_data','DESC')
            ->paginate($perPage)->withQueryString();
        return $activityDetail;
    }
    public function getByIDactivity($id)
    {
        $activityDetail = ActivityDetail::where('id_activity', $id)
            ->where('status', '=', '1')
            ->orderBy('activity_detail_id')
            ->paginate(10)->withQueryString();
        return $activityDetail;
    }

    public function updateStatus($id)
    {
        // ดึงข้อมูลที่ต้องการอัปเดตจากฐานข้อมูล
        $activityDetail = ActivityDetail::where("activity_detail_id", $id);

        // สลับสถานะจาก 0 เป็น 1 หรือจาก 1 เป็น 0
        $updated = $activityDetail->update([
            'status' => $activityDetail->first()->status == 0 ? 1 : 0
        ]);
        // คืนค่าข้อมูลที่ถูกอัปเดต
        return $activityDetail;
    }

    public function delete($id)
    {
        $result = [];

        $deleted = DB::transaction(function () use ($id, &$result) {
            $activityDetail = ActivityDetail::where('activity_detail_id', $id)->firstOrFail();
            $priceToSubtract = floatval($activityDetail->price);

            // 1. อัปเดต Activity
            $activity = Activity::findOrFail($activityDetail->id_activity);
            $activity->spend_money -= $priceToSubtract;
            $activity->save();
            $result['activity_remain_budget'] = floatval($activity->budget) - floatval($activity->spend_money);

            // 2. อัปเดต Project
            $project = Project::findOrFail($activity->id_project);
            $project->spend_money -= $priceToSubtract;
            $project->save();
            $result['project_remain_budget'] = floatval($project->budget) - floatval($project->spend_money);

            // 3. อัปเดต ActionPlan
            $actionplan = ActionPlan::findOrFail($project->id_action_plan);
            $actionplan->spend_money -= $priceToSubtract;
            $actionplan->save();
            $result['actionplan_remain_budget'] = floatval($actionplan->budget) - floatval($actionplan->spend_money);

            // 4. อัปเดต Strategic
            $strategic = Strategic::findOrFail($actionplan->id_strategic);
            $strategic->spend_money -= $priceToSubtract;
            $strategic->save();
            $result['strategic_remain_budget'] = floatval($strategic->budget) - floatval($strategic->spend_money);

            // 5. ลบ ActivityDetail
            $activityDetail->delete();

            return $activityDetail;
        });

        return response()->json([
            'deleted_activity_detail' => $deleted,
            'remain_budget_summary' => $result
        ]);
    }

    public function store(ActivityDetailDTO $activityDetailDTO)
    {
        $activityDetailDB = new ActivityDetail();
        $result = [];

        DB::transaction(function () use ($activityDetailDTO, $activityDetailDB, &$result) {
            // 1. เพิ่มข้อมูล activity_detail
            $activityDetailDB->detail = $activityDetailDTO->detail;
            $activityDetailDB->price = $activityDetailDTO->price;
            $activityDetailDB->start_date = $activityDetailDTO->start_date;
            $activityDetailDB->end_date = $activityDetailDTO->end_date;
            $activityDetailDB->station = $activityDetailDTO->station;

            $activityDetailDB->report_data = $activityDetailDTO->report_data;
            $activityDetailDB->id_employee = $activityDetailDTO->id_employee;
            $activityDetailDB->id_activity = $activityDetailDTO->id_activity;
            $activityDetailDB->save();

            // 2. อัปเดต activity
            $activity = Activity::findOrFail($activityDetailDTO->id_activity);
            $activity->spend_money += $activityDetailDTO->price;
            $activity->save();
            $result['activity_spend_money'] = $activity->budget  - $activity->spend_money;

            // 3. อัปเดต project
            $project = Project::findOrFail($activity->id_project);
            $project->spend_money += $activityDetailDTO->price;
            $project->save();
            $result['project_spend_money'] = $project->budget  -  $project->spend_money;

            // 4. อัปเดต action plan
            $actionplan = ActionPlan::findOrFail($project->id_action_plan);
            $actionplan->spend_money += $activityDetailDTO->price;
            $actionplan->save();
            $result['actionplan_spend_money'] = $actionplan->budget  - $actionplan->spend_money;

            // 5. อัปเดต strategic
            $strategic = Strategic::findOrFail($actionplan->id_strategic);
            $strategic->spend_money += $activityDetailDTO->price;
            $strategic->save();
            $result['strategic_spend_money'] = $strategic->budget  - $strategic->spend_money;
        });

        return response()->json([
            'activity_detail' => $activityDetailDB,
            'spend_money_summary' => $result
        ]);
    }
    public function update(ActivityDetailDTO $activityDetailDTO, $id)
    {
        $result = [];

        $activityDetailDB = DB::transaction(function () use ($activityDetailDTO, $id, &$result) {
            $activityDetailDB = ActivityDetail::where('activity_detail_id', $id)->firstOrFail();

            // ❗ ดึงราคาเดิมก่อนอัปเดต
            $oldPrice = floatval($activityDetailDB->price);
            $newPrice = floatval($activityDetailDTO->price);
            $priceDiff = $newPrice - $oldPrice;

            // ✅ อัปเดตข้อมูล activity_detail
            $activityDetailDB->detail = $activityDetailDTO->detail;
            $activityDetailDB->price = $newPrice;
            $activityDetailDB->start_date = $activityDetailDTO->start_date;
            $activityDetailDB->end_date = $activityDetailDTO->end_date;
            $activityDetailDB->station = $activityDetailDTO->station;

            $activityDetailDB->report_data = $activityDetailDTO->report_data;
            $activityDetailDB->id_employee = $activityDetailDTO->id_employee;
            $activityDetailDB->id_activity = $activityDetailDTO->id_activity;
            $activityDetailDB->save();

            // ✅ อัปเดต spend_money ของ Activity
            $activity = Activity::findOrFail($activityDetailDTO->id_activity);
            $activity->spend_money = floatval($activity->spend_money) + $priceDiff;
            $activity->save();
            $result['activity_spend_money'] = $activity->budget  - $activity->spend_money;

            // ✅ อัปเดต Project
            $project = Project::findOrFail($activity->id_project);
            $project->spend_money = floatval($project->spend_money) + $priceDiff;
            $project->save();
            $result['project_spend_money'] = $project->budget  - $project->spend_money;

            // ✅ อัปเดต ActionPlan
            $actionplan = ActionPlan::findOrFail($project->id_action_plan);
            $actionplan->spend_money = floatval($actionplan->spend_money) + $priceDiff;
            $actionplan->save();
            $result['actionplan_spend_money'] = $actionplan->budget  - $actionplan->spend_money;

            // ✅ อัปเดต Strategic
            $strategic = Strategic::findOrFail($actionplan->id_strategic);
            $strategic->spend_money = floatval($strategic->spend_money) + $priceDiff;
            $strategic->save();
            $result['strategic_spend_money'] = $strategic->budget  -  $strategic->spend_money;

            return $activityDetailDB;
        });

        return response()->json([
            'activity_detail' => $activityDetailDB,
            'spend_money_summary' => $result
        ]);
    }
}
