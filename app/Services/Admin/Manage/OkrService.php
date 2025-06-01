<?php

namespace App\Services\Admin\Manage;

use App\Dto\ActivityDTO;
use App\Models\Okr;
use App\Trait\Utils;
use App\Models\ActivityUser;
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
        $activity = Okr::findOrFail($id);
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
}
