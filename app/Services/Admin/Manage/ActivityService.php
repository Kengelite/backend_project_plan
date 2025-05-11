<?php

namespace App\Services\Admin\Manage;

use App\Dto\ActivityDTO;
use App\Models\Activity;
use App\Trait\Utils;
use App\Models\ActivityUser;
use Illuminate\Support\Facades\Auth;

class ActivityService
{
    use Utils;
    public function getAll()
    {
        $activity = Activity::paginate(10)->withQueryString();
        return $activity;
    }
    public function getByID($id)
    {
        $activity = Activity::findOrFail($id);
        return $activity;
    }

    public function getByIDactivity($id)
    {
        $activity = Activity::where('id_project',$id)
        ->where('status', 1)
        ->orderBy('id')
        ->paginate(10)
        ->withQueryString();
        return $activity;
    }
    public function getByIDactivityAdmin($id)
    {
        $activity = Activity::where('id_project',$id)
        ->orderBy('id')
        ->paginate(10)
        ->withQueryString();
        return $activity;
    }

    public function updateStatus($id)
    {
        // ดึงข้อมูลที่ต้องการอัปเดตจากฐานข้อมูล
        $activity = Activity::where("activity_id", $id);

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
    public function delete($id)
    {
        // $strategic = Strategic::findOrFail($id)->delete();
        // return $strategic;

        $activity = Activity::where('activity_id', $id)->firstOrFail();
        $activity->delete();
        return $activity;
    }
}
