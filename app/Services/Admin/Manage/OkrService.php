<?php

namespace App\Services\Admin\Manage;

use App\Dto\ActivityDTO;
use App\Models\Okr;
use App\Trait\Utils;
use App\Models\ActivityUser;
use Illuminate\Support\Facades\Auth;

class OkrService
{
    use Utils;
    public function getAll($perPage)
    {
        $activity = Okr::paginate($perPage)->withQueryString();
        return $activity;
    }
    public function getByID($id)
    {
        $activity = Okr::findOrFail($id);
        return $activity;
    }

    public function getByIDactivity($id)
    {
        $activity = Okr::where('id_project',$id)
        ->where('status', 1)
        ->orderBy('id')
        ->paginate(10)
        ->withQueryString();
        return $activity;
    }
    public function getByIDactivityAdmin($id)
    {
        $activity = Okr::where('id_project',$id)
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
        ->whereHas('activity', function ($query){
            $query->where('status',1);
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
