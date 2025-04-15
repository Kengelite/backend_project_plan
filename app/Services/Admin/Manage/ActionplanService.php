<?php

namespace App\Services\Admin\Manage;

use App\Dto\ActionPlanDTO;
use App\Models\ActionPlan;
use App\Trait\Utils;


class ActionplanService{
    use Utils;
    public function getAll(){
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
        $actionPlan = ActionPlan::where('strategic_id',$id)
        ->orderBy('action_plan_number')
        ->paginate(10)->withQueryString();
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

}