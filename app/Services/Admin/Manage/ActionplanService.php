<?php

namespace App\Services\Admin\Manage;

use App\Dto\ActionPlanDTO;
use App\Models\ActionPlan;
use App\Models\Strategic;
use App\Trait\Utils;
use Illuminate\Notifications\Action;
use Illuminate\Support\Facades\DB;

class ActionplanService
{
    use Utils;
    public function getAll()
    {
        $actionPlan = ActionPlan::paginate(10)->withQueryString();
        return $actionPlan;
    }
    public function getByID($id)
    {
        $actionPlan = ActionPlan::findOrFail($id);
        return $actionPlan;
    }

    public function getdataspendprice($id)
    {
        $actionPlanSum = ActionPlan::where('id_strategic', $id)
            ->selectRaw('
            COALESCE(SUM(budget), 0) as total_budget,
            COALESCE(SUM(spend_money), 0) as total_spend
        ')
            ->first();

        $strategic = Strategic::select('budget')
            ->where('strategic_id', $id)
            ->first();

        return [
            'total_budget' => $actionPlanSum->total_budget,
            'total_spend'             => $actionPlanSum->total_spend,
            'strategic_budget'        => $strategic->budget ?? 0,
            'remaining_budget'        => ($strategic->budget ?? 0) - $actionPlanSum->total_budget,
        ];
    }

    public function getByIDstrategic($id, $perPage)
    {
        $query = DB::table('action_plan')
            ->where('id_strategic', $id)
            ->whereNull('deleted_at');

        // ถ้าไม่ใช่ superadmin ให้เห็นเฉพาะที่เปิด
        if (auth()->user()->role != 2) {
            $query->where('status', 1);
        }

        $actionPlans = $query->orderBy('action_plan_number')
            ->paginate($perPage)
            ->withQueryString();

        // เพิ่ม field projects_count แบบแยก query
        $actionPlans->getCollection()->transform(function ($plan) {
            $projectsQuery = DB::table('project')
                ->where('id_action_plan', $plan->action_plan_id)
                ->whereNull('deleted_at');

            // ถ้าไม่ใช่ superadmin ให้นับเฉพาะ project ที่เปิด
            if (auth()->user()->role != 2) {
                $projectsQuery->where('status', 1);
            }

            $plan->projects_count = $projectsQuery->count();

            return $plan;
        });

        return $actionPlans;
    }

    public function updateStatus($id)
    {
        // ดึงข้อมูลที่ต้องการอัปเดตจากฐานข้อมูล
        $actionPlan = ActionPlan::where("action_plan_id", $id);

        // สลับสถานะจาก 0 เป็น 1 หรือจาก 1 เป็น 0
        $updated = $actionPlan->update([
            'status' => $actionPlan->first()->status == 0 ? 1 : 0
        ]);


        // คืนค่าข้อมูลที่ถูกอัปเดต
        return $actionPlan;
    }

    public function getByIDYear($id, $perPage)
    {
        $query = ActionPlan::where('id_year', $id)
            ->with('Strategic')
            ->whereNull('deleted_at');

        // ถ้าไม่ใช่ superadmin ให้เห็นเฉพาะ action plan ที่เปิด
        if (auth()->user()->role != 2) {
            $query->where('status', 1);
        }

        $actionPlan = $query->orderBy('action_plan_number')
            ->paginate($perPage)
            ->withQueryString();

        return $actionPlan;
    }
    // public function store(StrategicDTO $strategicDTO)
    // {
    //     $strategic = new Strategic();

    //     if ($strategicDTO->imageQrcode) {
    //         $resultFile = $this->storeFile($strategicDTO->imageQrcode, '/uploads/qr-code');
    //     }

    //     $strategic->status = $strategicDTO->status;
    //     $strategic->bank_account_name = $strategicDTO->strategicName;
    //     $strategic->bank_name = $strategicDTO->bankName;
    //     $strategic->bank_account_number = $strategicDTO->strategicNumber;
    //     $strategic->image_qrcode = @$resultFile['file_name'];
    //     $strategic->user_id = auth()->user()->id;

    //     $strategic->save();

    //     return  $strategic;
    // }

    public function delete($id)
    {
        // $strategic = Strategic::findOrFail($id)->delete();
        // return $strategic;

        $strategic = ActionPlan::where('action_plan_id', $id)->firstOrFail();
        $strategic->delete();
        return $strategic;
    }

    public function store(ActionPlanDTO $actionPlanDTO)
    {
        $actionplanDB = new ActionPlan();

        DB::transaction(function () use ($actionPlanDTO, $actionplanDB) {
            // Project
            $actionplanDB->action_plan_number = $actionPlanDTO->actionPlanNumber;
            $actionplanDB->name_ap = $actionPlanDTO->nameAp;
            $actionplanDB->id_strategic = $actionPlanDTO->idStrategic;
            $actionplanDB->id_year = $actionPlanDTO->idYear;
            $actionplanDB->budget = $actionPlanDTO->budget;
            $actionplanDB->save();
        });

        return  $actionplanDB;
    }
    public function update(ActionPlanDTO $actionPlanDTO, $id)
    {
        return DB::transaction(function () use ($actionPlanDTO, $id) {
            $actionplanDB = ActionPlan::where('action_plan_id', $id)->firstOrFail();

            $actionplanDB->action_plan_number = $actionPlanDTO->actionPlanNumber;
            $actionplanDB->name_ap = $actionPlanDTO->nameAp;
            $actionplanDB->id_strategic = $actionPlanDTO->idStrategic;
            $actionplanDB->id_year = $actionPlanDTO->idYear;
            $actionplanDB->budget = $actionPlanDTO->budget;

            $actionplanDB->save();

            return $actionplanDB;
        });
    }
}
