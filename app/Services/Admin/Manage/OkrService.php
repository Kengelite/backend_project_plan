<?php

namespace App\Services\Admin\Manage;

use App\Dto\ActivityDTO;
use App\Dto\OkrDTO;
use App\Models\Okr;
use App\Trait\Utils;
use App\Models\ActivityUser;
use App\Models\OkrUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OkrService
{
    use Utils;
    public function getAll($perPage, $year_id)
    {
        // $activity = Okr::paginate($perPage)->withQueryString();
        // $activity =

        $activity = DB::table('okr')
            // ->leftJoin('detail_year_OKR', 'okr.okr_id', '=', 'detail_year_OKR.id_okr')
            ->leftJoin('Unit', 'okr.id_unit', '=', 'Unit.unit_id')
            ->select(
                'okr.okr_id',
                'okr.okr_number',
                'okr.okr_name',
                'okr.status_report',
                'okr.goal',
                'okr.result',
                'okr.status',
                'okr.report_data',
                'okr.start_date',
                'okr.end_date',
                'okr.status_report',
                'okr.id_unit',
            )
            ->whereNull('okr.deleted_at')
            // ->whereNull('detail_year_OKR.deleted_at')
            ->where('okr.id_year', $year_id)
            ->orderBy('okr.okr_number')
            ->paginate($perPage)
            ->withQueryString();

        return $activity;
    }

    public function getAllUser($perPage, $year_id, $id_employee)
    {
        // $activity = Okr::paginate($perPage)->withQueryString();
        // $activity =

        $activity = DB::table('okr')
            // ->leftJoin('detail_year_OKR', 'okr.okr_id', '=', 'detail_year_OKR.id_okr')
            ->leftJoin('Unit', 'okr.id_unit', '=', 'Unit.unit_id')
            ->select(
                'okr.okr_id',
                'okr.okr_number',
                'okr.okr_name',
                'okr.status_report',
                'okr.goal',
                'okr.result',
                'okr.status',
                'okr.report_data',

                'okr.start_date',
                'okr.end_date',
                'okr.status_report',
            )
            ->whereNull('okr.deleted_at')
            // ->whereNull('detail_year_OKR.deleted_at')
            ->where('okr.id_year', $year_id)
            ->orderBy('okr.okr_number')
            ->paginate($perPage)
            ->withQueryString();

        return $activity;
    }
    public function getByID($id)
    {
        $activity = Okr::where('okr_id', $id)
            ->with([
                'year',
                'OkrUsers' => function ($q) {
                    $q->orderByDesc('main')  //  main=1 มาก่อน
                        ->orderBy('created_at'); // (เสริม) กันกรณีค่า main เท่ากัน
                },
                'OkrUsers.user',
                'OkrUsers.user.position',
            ])
            ->firstOrFail();

        return $activity;
    }

    public function getOkrUse()
    {
        $activity = Okr::where('status', 1)

            ->orderBy('okr_id')
            ->get();
        return $activity;
    }

    public function getByIDactivityAdmin($id)
    {
        $activity = Okr::where('id_project', $id)
            ->orderBy('id')
            ->paginate(10)
            ->withQueryString();
        return $activity;
    }

    public function updateStatus($id)
    {
        // ดึงข้อมูลที่ต้องการอัปเดตจากฐานข้อมูล
        $activity = Okr::where("okr_id", $id);

        // สลับสถานะจาก 0 เป็น 1 หรือจาก 1 เป็น 0
        $updated = $activity->update([
            'status' => $activity->first()->status == 0 ? 1 : 0
        ]);


        // คืนค่าข้อมูลที่ถูกอัปเดต
        return $activity;
    }

    public function getByIDUser($id)
    {
        $userId = Auth::id();

        $activityUser = ActivityUser::where('id_user', $userId)
            ->where('id_year', $id)
            ->whereHas('activity', function ($query) {
                $query->where('status', 1);
            })
            ->with('activity')
            ->paginate(10);

        return $activityUser;
    }

    public function getByIDYear($id, $perPage)
    {

        $project = Okr::where('id_year', $id)
            ->orderBy('okr_id')
            // ->with('strategic')
            ->paginate($perPage);

        return $project;
    }
    public function delete($id)
    {
        // $strategic = Strategic::findOrFail($id)->delete();
        // return $strategic;

        $activity = Okr::where('okr_id', $id)->firstOrFail();
        $activity->delete();
        return $activity;
    }
    public function update($id, OkrDTO $dataDTO)
    {
        return DB::transaction(function () use ($dataDTO, $id) {
            $okr = okr::where('okr_id', $id)->firstOrFail();

            $okr->okr_number    = $dataDTO->okrnumber;
            $okr->okr_name      = $dataDTO->okrname;
            // $okr->status_report = $dataDTO->status_report ?? $okr->status_report;
            $okr->goal          = $dataDTO->goal;
            $okr->result        = $dataDTO->result;
            $okr->report_data = $dataDTO->reportdata ?? '';
            $okr->start_date    = $dataDTO->startdate;
            $okr->end_date      = $dataDTO->enddate;
            $okr->id_unit       = $dataDTO->idunit;
            $okr->id_year       = $dataDTO->idyear;

            $okr->save();
            // Employee
            // $employeesDTO = $dataDTO->employeesDTO;
            // foreach ($employeesDTO as $key => $value) {
            //     $projectUserDB = new OkrUser();
            //     $projectUserDB->type = $value->type;
            //     $projectUserDB->main = $value->main;
            //     // $projectUserDB->status = $value->status;
            //     $projectUserDB->id_user = $value->idUser;
            //     // $projectUserDB->id_project = $dataDTO->okrId;
            //     $projectUserDB->id_year = $value->idYear ?? 0;
            //     $projectUserDB->save();
            // }

            // Teacher
            // $teachersDTO = $dataDTO->teachersDTO;
            // foreach ($teachersDTO as $key => $value) {
            //     $projectUserDB = new OkrUser();
            //     $projectUserDB->type = $value->type;
            //     $projectUserDB->main = $value->main;
            //     // $projectUserDB->status = $value->status;
            //     $projectUserDB->id_user = $value->idUser;
            //     // $projectUserDB->id_project = $dataDTO->okr_id;
            //     $projectUserDB->id_year = $value->idYear ?? 0;
            //     $projectUserDB->save();
            // }
            return $okr;
        });
    }

    public function store(OkrDTO $dataDTO)
    {
        $DB = new Okr();
        return DB::transaction(function () use ($dataDTO, $DB) {
            // $okr = okr::where('okr_id', $id)->firstOrFail();

            $DB->okr_number    = $dataDTO->okrnumber;
            $DB->okr_name      = $dataDTO->okrname;
            // $DB->status_report = $dataDTO->status_report ?? $DB->status_report;
            $DB->goal          = $dataDTO->goal;
            $DB->result        = $dataDTO->result;
            $DB->report_data = $dataDTO->reportdata ?? '';
            $DB->start_date    = $dataDTO->startdate;
            $DB->end_date      = $dataDTO->enddate;
            $DB->id_unit       = $dataDTO->idunit;
            $DB->id_year       = $dataDTO->idyear;

            $DB->save();
            // Employee
            $employeesDTO = $dataDTO->employeesDTO;
            foreach ($employeesDTO as $key => $value) {
                $projectUserDB = new OkrUser();
                $projectUserDB->type = $value->type;
                $projectUserDB->main = $value->main;
                $projectUserDB->id_user = $value->idUser;
                $projectUserDB->id_okr = $DB->okr_id;
                $projectUserDB->id_year = $value->idYear ?? 0;
                $projectUserDB->save();
            }

            // Teacher
            $teachersDTO = $dataDTO->teachersDTO;
            foreach ($teachersDTO as $key => $value) {
                $projectUserDB = new OkrUser();
                $projectUserDB->type = $value->type;
                $projectUserDB->main = $value->main;
                $projectUserDB->id_user = $value->idUser;
                $projectUserDB->id_okr = $DB->okr_id;
                $projectUserDB->id_year = $value->idYear ?? 0;
                $projectUserDB->save();
            }
            return $DB;
        });
    }
}
