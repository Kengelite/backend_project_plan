<?php

namespace App\Http\Controllers\Admin\Manage;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\HTTPSuccessResponse;
use App\Http\Resources\HTTPCreatedResponse;
use App\Services\Admin\Manage\OkrReportService;
use App\Http\Requests\Admin\Manage\OkrReportRequest;
use App\Trait\Utils;

class OkrReportController extends Controller
{
    use Utils;

    public function index(OkrReportService $okrReportService, Request $request)
    {
        try {
            $id_okr = $request->id_okr;
            $perPage = $request->input('per_page', 10);

            $result = $okrReportService->getByIDOkr($id_okr, $perPage);
            $res = new HTTPSuccessResponse(['data' => $result]);

            return response()->json($res, \Illuminate\Http\Response::HTTP_OK);
        } catch (\App\Exceptions\CustomException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'errors' => $e->getErrorDetails()
            ], $e->getStatusCode());
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], \Illuminate\Http\Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(OkrReportRequest $request, OkrReportService $okrReportService)
    {
        try {
            $okrReportDTO = $this->okrReportRequestToDTO($request);
            $result = $okrReportService->store($okrReportDTO);
            $res = new HTTPCreatedResponse(['data' => $result]);

            return response()->json($res, \Illuminate\Http\Response::HTTP_CREATED);
        } catch (\App\Exceptions\CustomException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'errors' => $e->getErrorDetails()
            ], $e->getStatusCode());
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], \Illuminate\Http\Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(OkrReportRequest $request, OkrReportService $okrReportService, $id)
    {
        try {
            $dto = $this->okrReportRequestToDTO($request);
            $dto->reportOkrId = $id;

            $result = $okrReportService->update($dto);

            return response()->json([
                'message' => 'แก้ไขรายงานผล OKR สำเร็จ',
                'data' => $result
            ], 200);
        } catch (\App\Exceptions\CustomException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'errors' => $e->getErrorDetails()
            ], $e->getStatusCode());
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id, OkrReportService $okrReportService)
    {
        try {
            $result = $okrReportService->delete($id);

            return response()->json([
                'message' => 'ลบรายงานผล OKR สำเร็จ',
                'data' => $result
            ], 200);
        } catch (\App\Exceptions\CustomException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'errors' => $e->getErrorDetails()
            ], $e->getStatusCode());
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    private function okrReportRequestToDTO(Request $request)
    {
        $dto = new \App\Dto\OkrReportDTO();
        $dto->reportOkrId = $request->report_okr_id ?? null;
        $dto->idOkr = $request->id_okr;
        $dto->idUser = auth()->id();
        $dto->reportDate = $request->report_date;
        $dto->resultValue = $request->result_value;
        $dto->detailLink = $request->detail_link;
        $dto->reportDetail = $request->report_detail;

        return $dto;
    }
}