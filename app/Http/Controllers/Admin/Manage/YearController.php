<?php

namespace App\Http\Controllers\Admin\Manage;

use App\Http\Controllers\Controller;
// use App\Http\Requests\Admin\Manage\yearRequest;
use App\Http\Resources\HTTPSuccessResponse;
use App\Services\Admin\Manage\YearService;
use App\Trait\Utils;
use Illuminate\Http\Request;

class YearController extends Controller
{
    public function index(YearService $yearService)
    {
        try {
            $result = $yearService->getAll();
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
}