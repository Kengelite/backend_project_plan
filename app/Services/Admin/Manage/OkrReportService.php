<?php

namespace App\Services\Admin\Manage;

use App\Dto\OkrReportDTO;
use App\Models\Okr;
use App\Models\OkrReport;
use App\Trait\Utils;
use Illuminate\Support\Facades\DB;
use App\Exceptions\CustomException;
use Carbon\Carbon;

class OkrReportService
{
    use Utils;

    public function store(OkrReportDTO $dataDTO)
    {
        return DB::transaction(function () use ($dataDTO) {
            $okr = Okr::where('okr_id', $dataDTO->idOkr)
                ->whereNull('deleted_at')
                ->first();

            if (!$okr) {
                throw new CustomException('ไม่พบข้อมูล OKR', 404);
            }

            $startDate = $this->normalizeDateToCarbon($okr->start_date);
            $endDate = $this->normalizeDateToCarbon($okr->end_date);
            $reportDate = $this->normalizeDateToCarbon($dataDTO->reportDate);

            if (!$startDate || !$endDate) {
                throw new CustomException('ไม่พบช่วงวันที่กำหนดให้รายงานของ OKR', 422);
            }

            if (!$reportDate) {
                throw new CustomException('รูปแบบวันที่รายงานไม่ถูกต้อง', 422);
            }

            if ($reportDate->lt($startDate) || $reportDate->gt($endDate)) {
                throw new CustomException('วันที่รายงานต้องอยู่ในช่วงวันที่ที่กำหนดให้รายงานของ OKR เท่านั้น', 422);
            }

            if ((float)$dataDTO->resultValue < 0) {
                throw new CustomException('ผลดำเนินการต้องไม่น้อยกว่า 0', 422);
            }

            if ((float)$dataDTO->resultValue > (float)$okr->goal) {
                throw new CustomException('ผลดำเนินการต้องไม่เกินค่าเป้าหมายของ OKR', 422);
            }

            $DB = new OkrReport();
            $DB->id_okr = $dataDTO->idOkr;
            $DB->id_user = $dataDTO->idUser;
            $DB->report_date = $this->normalizeDateForDatabase(
                $dataDTO->reportDate,
                $okr->start_date
            );
            $DB->result_value = $dataDTO->resultValue;
            $DB->detail_link = $dataDTO->detailLink;
            $DB->report_detail = $dataDTO->reportDetail;
            $DB->save();

            $this->syncLatestResultToOkr($dataDTO->idOkr);

            return $DB;
        });
    }

    public function update(OkrReportDTO $dataDTO)
    {
        return DB::transaction(function () use ($dataDTO) {
            $report = OkrReport::where('report_okr_id', $dataDTO->reportOkrId)
                ->whereNull('deleted_at')
                ->first();

            if (!$report) {
                throw new CustomException('ไม่พบข้อมูลรายงาน OKR', 404);
            }

            $okr = Okr::where('okr_id', $report->id_okr)
                ->whereNull('deleted_at')
                ->first();

            if (!$okr) {
                throw new CustomException('ไม่พบข้อมูล OKR', 404);
            }

            if ((float)$dataDTO->resultValue < 0) {
                throw new CustomException('ผลดำเนินการต้องไม่น้อยกว่า 0', 422);
            }

            if ((float)$dataDTO->resultValue > (float)$okr->goal) {
                throw new CustomException('ผลดำเนินการต้องไม่เกินค่าเป้าหมายของ OKR', 422);
            }

            if (empty(trim((string) $dataDTO->reportDetail))) {
                throw new CustomException('กรุณากรอกรายละเอียดรายงาน', 422);
            }

            if (mb_strlen((string) $dataDTO->reportDetail) > 255) {
                throw new CustomException('รายละเอียดรายงานต้องไม่เกิน 255 ตัวอักษร', 422);
            }

            if (empty(trim((string) $dataDTO->detailLink))) {
                throw new CustomException('กรุณากรอกลิงก์ข้อมูลรายละเอียด', 422);
            }

            $report->result_value = $dataDTO->resultValue;
            $report->report_detail = $dataDTO->reportDetail;
            $report->detail_link = $dataDTO->detailLink;
            $report->save();

            $this->syncLatestResultToOkr($report->id_okr);

            return $report;
        });
    }

    public function getByIDOkr($id, $perPage = 10)
    {
        return OkrReport::where('id_okr', $id)
            ->whereNull('deleted_at')
            ->orderByDesc('report_date')
            ->paginate($perPage);
    }

    public function delete($id)
    {
        $report = OkrReport::where('report_okr_id', $id)
            ->whereNull('deleted_at')
            ->first();

        if (!$report) {
            throw new CustomException('ไม่พบข้อมูลรายงาน OKR ที่ต้องการลบ', 404);
        }

        $okrId = $report->id_okr;
        $report->delete();

        $this->syncLatestResultToOkr($okrId);

        return $report;
    }

    private function syncLatestResultToOkr(string $okrId): void
    {
        $okr = Okr::where('okr_id', $okrId)
            ->whereNull('deleted_at')
            ->first();

        if (!$okr) {
            return;
        }

        $latestReport = OkrReport::where('id_okr', $okrId)
            ->whereNull('deleted_at')
            ->orderByDesc('report_date')
            ->orderByDesc('report_okr_id')
            ->first();

        if ($latestReport) {
            $okr->result = $latestReport->result_value;
            $okr->status_report = 1;
        } else {
            $okr->result = 0;
            $okr->status_report = 0;
        }

        $okr->save();
    }

    private function normalizeDateToCarbon(?string $date): ?Carbon
    {
        if (!$date) {
            return null;
        }

        $date = explode(' ', $date)[0];
        $parts = explode('-', $date);

        if (count($parts) !== 3) {
            return null;
        }

        [$year, $month, $day] = array_map('intval', $parts);

        if (!$year || !$month || !$day) {
            return null;
        }

        if ($year > 2400) {
            $year -= 543;
        }

        return Carbon::create($year, $month, $day)->startOfDay();
    }

    private function normalizeDateForDatabase(?string $inputDate, ?string $referenceDate): ?string
    {
        if (!$inputDate) {
            return null;
        }

        $inputDate = explode(' ', $inputDate)[0];
        $parts = explode('-', $inputDate);

        if (count($parts) !== 3) {
            return $inputDate;
        }

        [$year, $month, $day] = array_map('intval', $parts);

        if (!$year || !$month || !$day) {
            return $inputDate;
        }

        $refYear = null;
        if ($referenceDate) {
            $refYear = (int) explode('-', explode(' ', $referenceDate)[0])[0];
        }

        if ($refYear && $refYear > 2400) {
            if ($year < 2400) {
                $year += 543;
            }
            return sprintf('%04d-%02d-%02d', $year, $month, $day);
        }

        if ($year > 2400) {
            $year -= 543;
        }

        return sprintf('%04d-%02d-%02d', $year, $month, $day);
    }
}