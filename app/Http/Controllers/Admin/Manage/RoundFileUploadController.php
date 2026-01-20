<?php

namespace App\Http\Controllers\Admin\Manage;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Trait\Utils;

use App\Http\Resources\HTTPCreatedResponse;
use App\Http\Resources\HTTPSuccessResponse;
use App\Services\Admin\Manage\RoundFileUploadService;

class RoundFileUploadController extends Controller
{
    //
    use Utils;

    public function index(RoundFileUploadService $yearService, Request $request)
    {
        try {
            $perPage = $request->input('per_page', 10);
            $result = $yearService->getAll($perPage);
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

    public function uploaddata(RoundFileUploadService $yearService, Request $request)
    {
        try {
            $data = $request->input('data');
            // $file = $request->input('file_name');
            // $year = $request->input('year');
            $result = $yearService->uploaddata($data['data'], $data['file_name'], $data['year']);
            // $da = [
            //     "data" => $data,
            //     "file" => $file,
            //     "year" => $year,
            //     // 'data' => $result
            // ];
            $res = new HTTPCreatedResponse(['data' => $result]);
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
}
