<?php

namespace App\Services\Admin\Manage;

use App\Dto\ActivityDTO;
use App\Models\Principle;
use App\Trait\Utils;
use App\Models\ActivityUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Dto\PrincipleDTO;
class PrincipleService
{
    use Utils;
    public function getAll($perPage)
    {
        $principle = Principle::paginate($perPage)->withQueryString();
        return $principle;
    }
    public function getByID($id)
    {
        $principle = Principle::findOrFail($id);
        return $principle;
    }

    public function getprinciple()
    {
        $principle = Principle::where('status', 1)
        ->orderBy('principle_id')
        ->get();
        return $principle;
    }
    
    public function getByIDactivityAdmin($id)
    {
        $principle = Principle::where('id_project',$id)
        ->orderBy('id')
        ->paginate(10)
        ->withQueryString();
        return $principle;
    }

    public function updateStatus($id)
    {
        // ดึงข้อมูลที่ต้องการอัปเดตจากฐานข้อมูล
        $principle = Principle::where("principle_id", $id);

        // สลับสถานะจาก 0 เป็น 1 หรือจาก 1 เป็น 0
        $updated = $principle->update([
            'status' => $principle->first()->status == 0 ? 1 : 0
        ]);


        // คืนค่าข้อมูลที่ถูกอัปเดต
        return $principle;
    }

    public function getByIDUser($id)
    {
        $userId = Auth::id();

        $principleUser = ActivityUser::where('id_user', $userId)
        ->where('id_year', $id)
        ->whereHas('activity', function ($query){
            $query->where('status',1);
        })
        ->with('activity')
        ->paginate(10);

        return $principleUser;
    }

    public function getByIDYear($id, $perPage)
    {

        $project = Principle::where('id_year', $id)
            ->orderBy('principle_id')
            // ->with('strategic')
            ->paginate($perPage);

        return $project;
    }
    public function delete($id)
    {
        // $strategic = Strategic::findOrFail($id)->delete();
        // return $strategic;

        $principle = Principle::where('principle_id', $id)->firstOrFail();
        $principle->delete();
        return $principle;
    }

    public function store(PrincipleDTO $principleDTO)
    {
        $principleDB = new Principle();

        DB::transaction(function () use ($principleDTO, $principleDB) {
            // Project
            $principleDB->principle_name = $principleDTO->namePriciples;
            $principleDB->save();
        });

        return  $principleDB;
    }
    public function update(PrincipleDTO $principleDTO, $id)
    {
        return DB::transaction(function () use ($principleDTO, $id) {
            $princple = Principle::where('principle_id', $id)->firstOrFail();

            $princple->principle_name = $principleDTO->namePriciples;

            $princple->save();

            return $princple;
        });
    }
}
