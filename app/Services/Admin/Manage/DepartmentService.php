<?php

namespace App\Services\Admin\Manage;

use App\Dto\ActivityDTO;
use App\Models\Department;
use App\Trait\Utils;
use App\Models\ActivityUser;
use Illuminate\Support\Facades\Auth;
use App\Dto\DepartmentDTO;
use Illuminate\Support\Facades\DB;

class DepartmentService
{
    use Utils;
    public function getAll($perPage)
    {
        $activity = Department::paginate($perPage)->withQueryString();
        return $activity;
    }
    public function getByID($id)
    {
        $activity = Department::findOrFail($id);
        return $activity;
    }

    public function getDepartmentUser()
    {
        $activity = Department::where('status', 1)
            ->orderBy('departments_name')
            ->get();
            // ->withQueryString();
        return $activity;
    }
    public function getByIDactivityAdmin($id)
    {
        $activity = Department::where('id_project', $id)
            ->orderBy('id')
            ->paginate(10)
            ->withQueryString();
        return $activity;
    }

    public function updateStatus($id)
    {
        // ดึงข้อมูลที่ต้องการอัปเดตจากฐานข้อมูล
        $activity = Department::where("departments_id", $id);

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

        $project = Department::where('id_year', $id)
            ->orderBy('departments_id')
            // ->with('strategic')
            ->paginate($perPage);

        return $project;
    }
    public function delete($id)
    {
        // $strategic = Strategic::findOrFail($id)->delete();
        // return $strategic;

        $activity = Department::where('departments_id', $id)->firstOrFail();
        $activity->delete();
        return $activity;
    }

    public function store(DepartmentDTO $departmentDTO)
    {
        $departmentDB = new Department();

        DB::transaction(function () use ($departmentDTO, $departmentDB) {
            // Project
            $departmentDB->departments_name = $departmentDTO->nameDepartment;
            $departmentDB->save();
        });

        return  $departmentDB;
    }
    public function update(DepartmentDTO $departmentDTO, $id)
    {
        return DB::transaction(function () use ($departmentDTO, $id) {
            $department = Department::where('departments_id', $id)->firstOrFail();

            $department->departments_name = $departmentDTO->nameDepartment;

            $department->save();

            return $department;
        });
    }
}
