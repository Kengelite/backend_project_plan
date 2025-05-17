<?php

namespace App\Services\Admin\Manage;

use App\Dto\ActivityDTO;
use App\Models\position;
use App\Trait\Utils;
use App\Models\ActivityUser;
use Illuminate\Support\Facades\DB;
use App\Dto\PositionDTO;
use Illuminate\Support\Facades\Auth;

class PositionService
{
    use Utils;
    public function getAll($perPage)
    {
        $activity = position::paginate($perPage)->withQueryString();
        return $activity;
    }
    public function getByID($id)
    {
        $activity = position::findOrFail($id);
        return $activity;
    }

    public function getByIDactivity($id)
    {
        $activity = position::where('id_project', $id)
            ->where('status', 1)
            ->orderBy('id')
            ->paginate(10)
            ->withQueryString();
        return $activity;
    }
    public function getByIDactivityAdmin($id)
    {
        $activity = position::where('id_project', $id)
            ->orderBy('id')
            ->paginate(10)
            ->withQueryString();
        return $activity;
    }

    public function updateStatus($id)
    {
        // ดึงข้อมูลที่ต้องการอัปเดตจากฐานข้อมูล
        $activity = position::where("position_id", $id);

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

        $project = position::where('id_year', $id)
            ->orderBy('position_id')
            // ->with('strategic')
            ->paginate($perPage);

        return $project;
    }
    public function delete($id)
    {
        // $strategic = Strategic::findOrFail($id)->delete();
        // return $strategic;

        $activity = position::where('position_id', $id)->firstOrFail();
        $activity->delete();
        return $activity;
    }
    public function store(PositionDTO $positionDTO)
    {
        $positionDB = new position();

        DB::transaction(function () use ($positionDTO, $positionDB) {
            // Project
            $positionDB->position_name = $positionDTO->namePosition;
            $positionDB->save();
        });

        return  $positionDB;
    }
    public function update(PositionDTO $positionDTO, $id)
    {
        return DB::transaction(function () use ($positionDTO, $id) {
            $position = position::where('position_id', $id)->firstOrFail();

            $position->position_name = $positionDTO->namePosition;

            $position->save();

            return $position;
        });
    }

    
}
