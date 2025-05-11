<?php

namespace App\Services\Admin\Manage;

use App\Dto\ActionPlanDTO;
use App\Models\ActionPlan;
use App\Trait\Utils;
use Illuminate\Notifications\Action;

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

    public function getByIDstrategic($id)
    {
        $actionPlan = ActionPlan::where('id_strategic', $id)
            ->orderBy('action_plan_number')
            ->paginate(10)->withQueryString();
        return $actionPlan;
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
}
