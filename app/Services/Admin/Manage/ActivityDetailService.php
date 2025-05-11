<?php

namespace App\Services\Admin\Manage;

use App\Dto\ActivityDetailDTO;
use App\Models\ActivityDetail;
use App\Trait\Utils;

class ActivityDetailService{
    use Utils;
    public function getAll(){
        $activityDetail = ActivityDetail::paginate(10)->withQueryString();
        return $activityDetail;
    }
    public function getByID($id){
        $activityDetail = ActivityDetail::findOrFail($id);
        return $activityDetail;
    }
    public function getByIDactivityAdmin($id){
        $activityDetail = ActivityDetail::where('id_activity',$id)
        ->orderBy('activity_detail_id')
        ->paginate(10)->withQueryString();
        return $activityDetail;
    }
    public function getByIDactivity($id){
        $activityDetail = ActivityDetail::where('id_activity',$id)
        ->where('status','=','1')
        ->orderBy('activity_detail_id')
        ->paginate(10)->withQueryString();
        return $activityDetail;
    }

    public function updateStatus($id)
    {
        // ดึงข้อมูลที่ต้องการอัปเดตจากฐานข้อมูล
        $activityDetail = ActivityDetail::where("activity_detail_id", $id);

        // สลับสถานะจาก 0 เป็น 1 หรือจาก 1 เป็น 0
        $updated = $activityDetail->update([
            'status' => $activityDetail->first()->status == 0 ? 1 : 0
        ]);
        // คืนค่าข้อมูลที่ถูกอัปเดต
        return $activityDetail;
    }

    public function delete($id)
    {
        // $strategic = Strategic::findOrFail($id)->delete();
        // return $strategic;

        $project = ActivityDetail::where('activity_detail_id', $id)->firstOrFail();
        $project->delete();
        return $project;
    }

}
