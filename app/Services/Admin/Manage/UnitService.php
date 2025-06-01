<?php

namespace App\Services\Admin\Manage;

use App\Dto\ActivityDTO;
use App\Models\Unit;
use App\Trait\Utils;
use App\Models\ActivityUser;
use Illuminate\Support\Facades\Auth;

class UnitService
{
    use Utils;
    public function getAll($perPage)
    {
        $data = Unit::paginate($perPage)->withQueryString();
        return $data;
    }
    public function getUser()
    {
        $data = Unit::select('unit_id', 'unit_name') // ระบุเฉพาะ field ที่ต้องการ
            ->where('status', '1')
            ->orderBy('unit_name')
            ->get();

        return $data;
    }

    public function updateStatus($id)
    {
        // ดึงข้อมูลที่ต้องการอัปเดตจากฐานข้อมูล
        $user = Unit::where("activity_id", $id);

        // สลับสถานะจาก 0 เป็น 1 หรือจาก 1 เป็น 0
        $updated = $user->update([
            'status' => $user->first()->status == 0 ? 1 : 0
        ]);


        // คืนค่าข้อมูลที่ถูกอัปเดต
        return $user;
    }

    public function getByIDUser($id)
    {
        $userId = Auth::id();

        $userUser = ActivityUser::where('id_user', $userId)
            ->where('id_year', $id)
            ->whereHas('activity', function ($query) {
                $query->where('status', 1);
            })
            ->with('activity')
            ->paginate(10);

        return $userUser;
    }

    public function getByIDYear($id, $perPage)
    {

        $project = Unit::where('id_year', $id)
            ->orderBy('activity_id')
            // ->with('strategic')
            ->paginate($perPage);

        return $project;
    }
    public function delete($id)
    {
        // $strategic = Strategic::findOrFail($id)->delete();
        // return $strategic;

        $user = Unit::where('id', $id)->firstOrFail();
        $user->delete();
        return $user;
    }
}
