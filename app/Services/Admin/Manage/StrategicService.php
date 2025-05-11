<?php

namespace App\Services\Admin\Manage;

use App\Dto\StrategicDTO;
use App\Models\Strategic;
use App\Models\Year;
use App\Trait\Utils;

class StrategicService
{
    use Utils;
    public function getAll()
    {
        $strategics = Strategic::paginate(10)->withQueryString();
        return $strategics;
    }

    public function getByID($id)
    {
        $strategic = Strategic::findOrFail($id);
        return $strategic;
    }

    public function getLatestYearStrategic()
    {
        // ดึง year_id ที่มี year มากที่สุด
        $latestYear = Year::orderByDesc('year')->first();

        $strategic = Strategic::where('id_year', $latestYear->year_id)
            ->orderBy('strategic_number')
            ->paginate(10)
            ->withQueryString();

        return $strategic;
    }
    public function getByYear($id)
    {
        $strategic = Strategic::where('id_year',$id)
        ->orderBy('strategic_number')
        ->paginate(10)
        ->withQueryString();

        return $strategic;
    }
    public function updateStatus($id)
    {
        // ดึงข้อมูลที่ต้องการอัปเดตจากฐานข้อมูล
        $strategic = Strategic::where("strategic_id", $id);

        // สลับสถานะจาก 0 เป็น 1 หรือจาก 1 เป็น 0
        $updated = $strategic->update([
            'status' => $strategic->first()->status == 0 ? 1 : 0
        ]);


        // คืนค่าข้อมูลที่ถูกอัปเดต
        return $strategic;
    }

    public function delete($id)
    {
        // $strategic = Strategic::findOrFail($id)->delete();
        // return $strategic;

        $strategic = Strategic::where('strategic_id', $id)->firstOrFail();
        $strategic->delete();
        return $strategic;
    }

    // public function store(StrategicDTO $strategicDTO)
    // {
    //     $strategic = new Strategic();

    //     if ($strategicDTO->imageQrcode) {
    //         $resultFile = $this->storeFile($strategicDTO->imageQrcode, '/uploads/qr-code');
    //     }

    //     $strategic->status = $strategicDTO->status;
    //     $strategic->bank_account_name = $strategicDTO->strategicName;
    //     $strategic->bank_name = $strategicDTO->bankName;
    //     $strategic->bank_account_number = $strategicDTO->strategicNumber;
    //     $strategic->image_qrcode = @$resultFile['file_name'];
    //     $strategic->user_id = auth()->user()->id;

    //     $strategic->save();

    //     return  $strategic;
    // }

    // public function update($id, StrategicDTO $strategicDTO)
    // {
    //     $strategicDB = Strategic::findOrFail($id);

    //     if (!$strategicDB) {
    //         throw new \App\Exceptions\CustomException("Bank account not found", \Illuminate\Http\Response::HTTP_NOT_FOUND);
    //     }

    //     if ($strategicDTO->imageQrcode) {
    //         $oldImage = '/uploads/qr-code/' . $strategicDB->imageQrcode;
    //         $result = @$this->deleteFile($oldImage);
    //         $resultFile = $this->storeFile($strategicDTO->imageQrcode, '/uploads/qr-code');
    //         $strategicDB->image_qrcode = $resultFile['file_name'];
    //     }

    //     $strategicDB->status = $strategicDTO->status;
    //     $strategicDB->bank_account_name = $strategicDTO->strategicName;
    //     $strategicDB->bank_name = $strategicDTO->bankName;
    //     $strategicDB->bank_account_number = $strategicDTO->strategicNumber;
    //     $strategicDB->user_id = auth()->user()->id;

    //     $strategicDB->save();

    //     return  $strategicDB;
    // }
}
