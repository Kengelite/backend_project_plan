<?php

namespace App\Services\Admin\Manage;

use App\Dto\ActionPlanDTO;
use App\Models\ActionPlan;
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
        $result = ActionPlan::where('id_strategic', $id)
            ->whereNull('deleted_at')
            ->selectRaw('SUM(budget) as total_budget, SUM(spend_money) as total_spend')
            ->first();

        return   $result;
    }

    public function getByIDstrategic($id, $perPage)
    {

        // $actionPlan = DB::table('action_plan')
        // ->leftJoin('project', 'action_plan.action_plan_id', '=', 'project.id_action_plan')
        // ->select(
        //     'action_plan_id',
        //     'action_plan_number',
        //     'name_ap',
        //     'action_plan.budget',
        //     'action_plan.spend_money',
        //     'action_plan.status',
        //     'id_strategic',
        //     'action_plan.id_year',
        //     DB::raw('COUNT(DISTINCT project.project_id) as projects_count')
        // )
        // ->where('id_strategic', $id)
        // ->whereNull('action_plan.deleted_at')
        // ->whereNull('project.deleted_at')
        // ->groupBy(
        //     'action_plan_id',
        //     'action_plan_number',
        //     'name_ap',
        //     'action_plan.budget',
        //     'action_plan.spend_money',
        //     'action_plan.status',
        //     'id_strategic',
        //     'action_plan.id_year',
        // )
        // ->orderBy('action_plan_number')
        // ->paginate($perPage)
        // ->withQueryString();




        $actionPlans = DB::table('action_plan')
            ->where('id_strategic', $id)
            ->whereNull('deleted_at')
            ->orderBy('action_plan_number')
            ->paginate($perPage)
            ->withQueryString();

        // เพิ่ม field projects_count แบบแยก query
        $actionPlans->getCollection()->transform(function ($plan) {
            $projectsCount = DB::table('project')
                ->where('id_action_plan', $plan->action_plan_id)
                ->whereNull('deleted_at')
                ->count();

            $plan->projects_count = $projectsCount;
            return $plan;
        });


        // $actionPlan = ActionPlan::where('id_strategic', $id)
        //     ->orderBy('action_plan_number')
        //     ->paginate($perPage)->withQueryString();
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

        $actionPlan = ActionPlan::where('id_year', $id)
            ->orderBy('action_plan_number')
            ->with('strategic')
            ->paginate($perPage);

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
