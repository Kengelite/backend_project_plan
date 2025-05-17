<?php

namespace App\Services\Admin\Manage;

use App\Dto\YearDTO;
use App\Models\Year;
use App\Trait\Utils;
use Illuminate\Support\Facades\DB;
class YearService
{
    use Utils;
    public function getAll()
    {
        $year = Year::orderByDesc('year')->paginate(10)->withQueryString();;
        return $year;
    }

    public function getAllYearUser()
    {
        $year = Year::where('status',1)
        ->orderByDesc('year')
        ->paginate(10)->withQueryString();;
        return $year;
    }
    public function updateStatus($id)
    {
        // ดึงข้อมูลที่ต้องการอัปเดตจากฐานข้อมูล
        $year = Year::where("year_id", $id);

        // สลับสถานะจาก 0 เป็น 1 หรือจาก 1 เป็น 0
        $updated = $year->update([
            'status' => $year->first()->status == 0 ? 1 : 0
        ]);


        // คืนค่าข้อมูลที่ถูกอัปเดต
        return $year;
    }
    public function delete($id)
    {
        // $strategic = Strategic::findOrFail($id)->delete();
        // return $strategic;

        $year = Year::where('year_id', $id)->firstOrFail();
        $year->delete();
        return $year;
    }
    public function store(YearDTO $yearDTO)
    {
        $yearDB = new Year();

        DB::transaction(function () use ($yearDTO, $yearDB) {
            // Project
            $yearDB->year = $yearDTO->nameYear;
            $yearDB->save();
        });

        return  $yearDB;
    }
    public function update(YearDTO $yearDTO, $id)
    {
        return DB::transaction(function () use ($yearDTO, $id) {
            $year = Year::where('year_id', $id)->firstOrFail();

            $year->year = $yearDTO->nameYear;

            $year->save();

            return $year;
        });
    }
}
