<?php

namespace App\Services\Admin\Manage;

use App\Dto\StrategicDTO;
use App\Models\Strategic;
use App\Models\Year;
use App\Models\ActionPlan;
use App\Models\Project;
use App\Trait\Utils;
use Illuminate\Support\Facades\DB;

class StrategicService
{
    use Utils;
    public function getAll()
    {
        $strategics = Strategic::paginate(10)->withQueryString();
        return $strategics;
    }

    public function getByID($id)
    {
        $strategic = Strategic::findOrFail($id);
        return $strategic;
    }

    public function getLatestYearStrategic()
    {
        // ดึง year_id ที่มี year มากที่สุด
        $latestYear = Year::orderByDesc('year')->first();

        $strategic = Strategic::where('id_year', $latestYear->year_id)
            ->orderBy('strategic_number')
            ->paginate(10)
            ->withQueryString();

        return $strategic;
    }

    public function getByYearForAdd($id)
    {

        $strategic =
            DB::table('strategic')
            ->leftJoin('year', 'strategic.id_year', 'year.year_id')
            ->select(
                'strategic.strategic_id',
                'strategic.strategic_name',
                'strategic.strategic_number'
            )
            ->where('strategic.id_year', $id)
            ->where('strategic.status', '1')
            ->orderBy('strategic.strategic_number')
            ->paginate(10)
            ->withQueryString();
        // Strategic::where('id_year', $id)
        //     ->orderBy('id_year')
        //     ->paginate(10)
        //     ->withQueryString();

        return $strategic;
    }
    public function getByYear($id)
    {
        // $strategics = DB::table('strategic')
        //     ->leftJoin('action_plan', 'strategic.strategic_id', '=', 'action_plan.id_strategic')
        //     ->leftJoin('project', 'action_plan.action_plan_id', '=', 'project.id_action_plan')
        //     ->leftJoin('activity', 'project.project_id', '=', 'activity.id_project')
        //     ->select(
        //         'strategic.strategic_id',
        //         'strategic.strategic_name',
        //         'strategic.strategic_number',
        //         'strategic.budget',
        //         'strategic.spend_money',
        //         'strategic.status',
        //         'strategic.id_year',
        //         DB::raw('COUNT(DISTINCT project.project_id) as projects_count'),
        //         DB::raw('COUNT(DISTINCT action_plan.action_plan_id) as action_plan_count')
        //     )
        //     ->where('strategic.id_year', $id)
        //     ->whereNull('strategic.deleted_at')
        //     ->whereNull('action_plan.deleted_at')
        //     ->whereNull('project.deleted_at')
        //     ->whereNull('activity.deleted_at')
        //     ->groupBy(
        //         'strategic.strategic_id',
        //         'strategic.strategic_name',
        //         'strategic.strategic_number',
        //         'strategic.budget',
        //         'strategic.spend_money',
        //         'strategic.status',
        //          'strategic.id_year'

        //     )
        //     ->orderBy('strategic.strategic_number')
        //     ->paginate(10)
        //     ->withQueryString();

        $strategics = Strategic::where('id_year', $id)
            ->whereNull('deleted_at')
            ->orderBy('strategic_number')
            ->paginate(10)
            ->withQueryString();

        foreach ($strategics as $strategic) {
            $strategic->action_plan_count = ActionPlan::where('id_strategic', $strategic->strategic_id)
                ->whereNull('deleted_at')
                ->count();

            $strategic->projects_count = Project::whereIn('id_action_plan', function ($query) use ($strategic) {
                $query->select('action_plan_id')
                    ->from('action_plan')
                    ->where('id_strategic', $strategic->strategic_id)
                    ->whereNull('deleted_at');
            })
                ->whereNull('deleted_at')
                ->count();
        }
        return $strategics;
    }
    public function updateStatus($id)
    {
        // ดึงข้อมูลที่ต้องการอัปเดตจากฐานข้อมูล
        $strategic = Strategic::where("strategic_id", $id);

        // สลับสถานะจาก 0 เป็น 1 หรือจาก 1 เป็น 0
        $updated = $strategic->update([
            'status' => $strategic->first()->status == 0 ? 1 : 0
        ]);


        // คืนค่าข้อมูลที่ถูกอัปเดต
        return $strategic;
    }

    public function delete($id)
    {
        // $strategic = Strategic::findOrFail($id)->delete();
        // return $strategic;

        $strategic = Strategic::where('strategic_id', $id)->firstOrFail();
        $strategic->delete();
        return $strategic;
    }

    public function store(StrategicDTO $strategicDTO)
    {
        $strategicDB = new Strategic();

        DB::transaction(function () use ($strategicDTO, $strategicDB) {
            // Project
            $strategicDB->strategic_number = $strategicDTO->numberStrategic;
            $strategicDB->strategic_name = $strategicDTO->nameStrategic;
            $strategicDB->id_year = $strategicDTO->idYear;
            $strategicDB->budget = $strategicDTO->budget;
            $strategicDB->save();
        });

        return  $strategicDB;
    }
    public function update(StrategicDTO $strategicDTO, $id)
    {
        return DB::transaction(function () use ($strategicDTO, $id) {
            $strategic = Strategic::where('strategic_id', $id)->firstOrFail();

            $strategic->strategic_number = $strategicDTO->numberStrategic;
            $strategic->strategic_name = $strategicDTO->nameStrategic;
            $strategic->id_year = $strategicDTO->idYear;
            $strategic->budget = $strategicDTO->budget;

            $strategic->save();

            return $strategic;
        });
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

    // public function update($id, StrategicDTO $strategicDTO)
    // {
    //     $strategicDB = Strategic::findOrFail($id);

    //     if (!$strategicDB) {
    //         throw new \App\Exceptions\CustomException("Bank account not found", \Illuminate\Http\Response::HTTP_NOT_FOUND);
    //     }

    //     if ($strategicDTO->imageQrcode) {
    //         $oldImage = '/uploads/qr-code/' . $strategicDB->imageQrcode;
    //         $result = @$this->deleteFile($oldImage);
    //         $resultFile = $this->storeFile($strategicDTO->imageQrcode, '/uploads/qr-code');
    //         $strategicDB->image_qrcode = $resultFile['file_name'];
    //     }

    //     $strategicDB->status = $strategicDTO->status;
    //     $strategicDB->bank_account_name = $strategicDTO->strategicName;
    //     $strategicDB->bank_name = $strategicDTO->bankName;
    //     $strategicDB->bank_account_number = $strategicDTO->strategicNumber;
    //     $strategicDB->user_id = auth()->user()->id;

    //     $strategicDB->save();

    //     return  $strategicDB;
    // }
}
