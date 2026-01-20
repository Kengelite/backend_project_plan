<?php

namespace App\Services\Admin\Manage;

use App\Models\ActivityDetailSpendmoney;

use App\Models\ActivitySpendmoney;
use App\Models\ActivityDetail;
use App\Models\Activity;
use App\Models\Project;
use App\Models\Strategic;
use App\Models\ActionPlan;
use App\Trait\Utils;
use Illuminate\Support\Facades\DB;
use App\Dto\ActivitiyDetailSpendMoneyDTO;
use Illuminate\Validation\ValidationException;

class ActivityDetailSpendMoneyService
{

    use Utils;



    public function update($id, ActivitiyDetailSpendMoneyDTO $dto)
    {
        return DB::transaction(function () use ($id, $dto) {

            // 3) ยอดรวมของแถว detail ทั้งหมดใต้ spendmoney เดียวกัน 550
            $total = ActivityDetailSpendmoney::whereNull('deleted_at')
                ->where('activity_detail_spendmoney_id', $id)
                ->select(DB::raw('SUM(price * amount) as total'))
                ->value('total') ?? 0.0;

            $priceToSubtract = (float) $total - ($dto->price * $dto->amount);

            // 5) อัปเดต ActivityDetail
            $activitySpend = ActivityDetail::findOrFail($dto->id_activity_detail);
            $activity = Activity::findOrFail($activitySpend->id_activity);
            //  NEW: เช็คงบก่อนบันทึก
            $newSpendMoney = (float) $activity->spend_money - $priceToSubtract;
            // if ($newSpendMoney > $activity->budget) {
            //     throw ValidationException::withMessages([
            //         'budget' => ["งบประมาณกิจกรรมไม่เพียงพอ "]
            //     ]);
            //     // หรือ: abort(422, "งบประมาณกิจกรรมไม่เพียงพอ");
            // }

            // ผ่าน → อัปเดตต่อ
            $activitySpend->price -= $priceToSubtract;
            $activitySpend->save();
            $result['activity_remain_budget'] = (float) $activitySpend->budget - (float) $activitySpend->spend_money;

            // 1) อัปเดตแถว ActivityDetailSpendmoney
            $detailSpend = ActivityDetailSpendmoney::findOrFail($id);
            $detailSpend->id_activity_detail     = $dto->id_activity_detail;
            $detailSpend->id_activity_spendmoney = $dto->id_activity_spendmoney;
            $detailSpend->price                  = $dto->price;
            $detailSpend->amount                 = $dto->amount;
            $detailSpend->save();

            $result = [];

            // 5) อัปเดต Activity

            $activity->spend_money -= $priceToSubtract;
            $activity->save();
            $result['activity_remain_budget'] = (float) $activity->budget - (float) $activity->spend_money;

            // 6) อัปเดต Project
            $project = Project::findOrFail($activity->id_project);
            $project->spend_money -= $priceToSubtract;
            $project->save();
            $result['project_remain_budget'] = (float) $project->budget - (float) $project->spend_money;

            // 7) อัปเดต ActionPlan
            $actionplan = ActionPlan::findOrFail($project->id_action_plan);
            $actionplan->spend_money -= $priceToSubtract;
            $actionplan->save();
            $result['actionplan_remain_budget'] = (float) $actionplan->budget - (float) $actionplan->spend_money;

            // 8) อัปเดต Strategic
            $strategic = Strategic::findOrFail($actionplan->id_strategic);
            $strategic->spend_money -= $priceToSubtract;
            $strategic->save();
            $result['strategic_remain_budget'] = (float) $strategic->budget - (float) $strategic->spend_money;

            return [
                'updated_activity_detail_spendmoney' => $detailSpend->fresh(),
                'anchor_activity_spendmoney'         => $activitySpend,
                'remain_budget_summary'              => $result,
            ];
        });
    }
    public function delete($id)
    {
        return DB::transaction(function () use ($id) {

            // ดึงแถวที่ต้องการลบ
            $detailSpend = ActivityDetailSpendmoney::findOrFail($id);

            // หา ActivityDetail ที่แถวนี้อยู่ใต้
            $activitySpend = ActivityDetail::findOrFail($detailSpend->id_activity_detail);
            $activity = Activity::findOrFail($activitySpend->id_activity);

            // คำนวณเงินที่จะเอาออก (price * amount เดิม)
            $priceToSubtract = (float) $detailSpend->price * (float) $detailSpend->amount;

            // เช็คงบก่อนลบ (ห้ามติดลบ)
            $newSpendMoney = (float) $activity->spend_money - $priceToSubtract;
            if ($newSpendMoney > $activity->budget) {
                throw new \Exception("งบประมาณไม่เพียงพอ ไม่สามารถลบรายการได้");
            }

            // อัปเดต ActivityDetail
            $activitySpend->price -= $priceToSubtract;
            if ($activitySpend->price < 0) {
                $activitySpend->price = 0; // กันลบเกิน
            }
            $activitySpend->save();

            $result = [];

            // อัปเดต Activity
            $activity->spend_money = $newSpendMoney;
            $activity->save();
            $result['activity_remain_budget'] = (float) $activity->budget - (float) $activity->spend_money;

            // อัปเดต Project
            $project = Project::findOrFail($activity->id_project);
            $project->spend_money -= $priceToSubtract;
            $project->save();
            $result['project_remain_budget'] = (float) $project->budget - (float) $project->spend_money;

            // อัปเดต ActionPlan
            $actionplan = ActionPlan::findOrFail($project->id_action_plan);
            $actionplan->spend_money -= $priceToSubtract;
            $actionplan->save();
            $result['actionplan_remain_budget'] = (float) $actionplan->budget - (float) $actionplan->spend_money;

            // อัปเดต Strategic
            $strategic = Strategic::findOrFail($actionplan->id_strategic);
            $strategic->spend_money -= $priceToSubtract;
            $strategic->save();
            $result['strategic_remain_budget'] = (float) $strategic->budget - (float) $strategic->spend_money;

            // Soft Delete DetailSpend
            $detailSpend->delete();

            return [
                'deleted_activity_detail_spendmoney' => $detailSpend,
                'anchor_activity_spendmoney'         => $activitySpend,
                'remain_budget_summary'              => $result,
            ];
        });
    }
    public function store(ActivitiyDetailSpendMoneyDTO $dto)
    {
        return DB::transaction(function () use ($dto) {

            // lockForUpdate กรณีมีผู้ใช้ 2 คน แก้ไขค่าใช้จ่ายรายการเดียวกันในเวลาเดียวกัน
            // 1) คำนวณยอดบรรทัดใหม่ (delta)
            $newLineTotal = (float) $dto->price * (float) $dto->amount;
            $delta = $newLineTotal; // create = ไม่มีค่าเก่า

            // 2) ล็อกโครงสร้างสายโซ่ให้ครบ
            // 2.1 ล็อก ActivityDetail (เจ้าของรายการย่อย)
            $activityDetail = ActivityDetail::where('activity_detail_id', $dto->id_activity_detail)
                ->lockForUpdate()
                ->firstOrFail();

            // 2.2 ล็อก Activity (เจ้าของ ActivityDetail)
            $activity = Activity::where('activity_id', $activityDetail->id_activity)
                ->lockForUpdate()
                ->firstOrFail();

            // 2.3 (ถ้าต้องการยืนยัน anchor) ล็อก ActivitySpendmoney ที่อ้างถึง
            // ปรับตามคีย์จริงของคุณ (ถ้าไม่มีโมเดลนี้ให้คอมเมนต์ออกได้)
            // $activitySpend = ActivitySpendmoney::where('activity_spendmoney_id', $dto->id_activity_spendmoney)
            //     ->lockForUpdate()
            //     ->firstOrFail();

            // 3) เช็คงบประมาณก่อนบันทึก
            $prospectiveSpend = (float) $activity->spend_money + $delta;
            // if ($prospectiveSpend > $activity->budget) {
            //     throw ValidationException::withMessages([
            //         'budget' => ["งบประมาณกิจกรรมไม่เพียงพอ "]
            //     ]);
            //     // หรือ: abort(422, "งบประมาณกิจกรรมไม่เพียงพอ");
            // }
            // 4) สร้างแถว ActivityDetailSpendmoney
            $detailSpend = new ActivityDetailSpendmoney();
            $detailSpend->id_activity_detail     = $dto->id_activity_detail;
            $detailSpend->id_activity_spendmoney = $dto->id_activity_spendmoney;
            $detailSpend->price                  = $dto->price;
            $detailSpend->amount                 = $dto->amount;
            $detailSpend->save();

            // 5) อัปเดตยอดรวมที่ ActivityDetail (กันติดลบด้วย max(0, ...))
            $activityDetail->price = max(0, (float) $activityDetail->price + $delta);
            $activityDetail->save();

            // 6) อัปเดต Activity
            $activity->spend_money = $prospectiveSpend;
            $activity->save();

            // 7) อัปเดต Project
            $project = Project::where('project_id', $activity->id_project)->lockForUpdate()->firstOrFail();
            $project->spend_money = (float) $project->spend_money + $delta;
            $project->save();

            // 8) อัปเดต ActionPlan
            $actionplan = ActionPlan::where('action_plan_id', $project->id_action_plan)->lockForUpdate()->firstOrFail();
            $actionplan->spend_money = (float) $actionplan->spend_money + $delta;
            $actionplan->save();

            // 9) อัปเดต Strategic
            $strategic = Strategic::where('strategic_id', $actionplan->id_strategic)->lockForUpdate()->firstOrFail();
            $strategic->spend_money = (float) $strategic->spend_money + $delta;
            $strategic->save();

            // 10) คืนผลลัพธ์
            return [
                'created_activity_detail_spendmoney' => $detailSpend->fresh(),
                'anchor_activity_detail'             => $activityDetail->fresh(),
                'remain_budget_summary'              => [
                    'activity_remain_budget'   => (float) $activity->budget - (float) $activity->spend_money,
                    'project_remain_budget'    => (float) $project->budget - (float) $project->spend_money,
                    'actionplan_remain_budget' => (float) $actionplan->budget - (float) $actionplan->spend_money,
                    'strategic_remain_budget'  => (float) $strategic->budget - (float) $strategic->spend_money,
                ],
                'delta_applied'                      => $delta,
            ];
        });
    }
}
