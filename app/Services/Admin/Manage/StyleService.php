<?php

namespace App\Services\Admin\Manage;

use App\Dto\ActivityDTO;
use App\Models\Style;
use App\Trait\Utils;
use App\Models\ActivityUser;
use App\Dto\TypeDTO;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StyleService
{
    use Utils;
    public function getAll($perPage)
    {
        $activity = Style::paginate($perPage)->withQueryString();
        return $activity;
    }
    public function getByID($id)
    {
        $activity = Style::findOrFail($id);
        return $activity;
    }

    public function getTypeuser()
    {
        $data = Style::where('status', 1)
        ->get();
        // ->withQueryString();
        return $data;
    }
    public function getByIDactivityAdmin($id)
    {
        $activity = Style::where('id_project',$id)
        ->orderBy('id')
        ->paginate(10)
        ->withQueryString();
        return $activity;
    }

    public function updateStatus($id)
    {
        // ดึงข้อมูลที่ต้องการอัปเดตจากฐานข้อมูล
        $activity = Style::where("style_id", $id);

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

        $project = Style::where('id_year', $id)
            ->orderBy('style_id')
            // ->with('strategic')
            ->paginate($perPage);

        return $project;
    }
    public function delete($id)
    {
        // $strategic = Strategic::findOrFail($id)->delete();
        // return $strategic;

        $activity = Style::where('style_id', $id)->firstOrFail();
        $activity->delete();
        return $activity;
    }
    public function store(TypeDTO $typeDTO)
    {
        $departmentDB = new Style();

        DB::transaction(function () use ($typeDTO, $departmentDB) {
            // Project
            $departmentDB->style_name = $typeDTO->nameStyle;
            $departmentDB->save();
        });

        return  $departmentDB;
    }
    public function update(TypeDTO $typeDTO, $id)
    {
        return DB::transaction(function () use ($typeDTO, $id) {
            $department = Style::where('style_id', $id)->firstOrFail();

            $department->style_name = $typeDTO->nameStyle;

            $department->save();

            return $department;
        });
    }
}
