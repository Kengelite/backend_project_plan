<?php

namespace App\Http\Controllers\Admin\Manage;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Resources\HTTPSuccessResponse;
use App\Services\Admin\Manage\ActivityDetailService;
use App\Trait\Utils;
use App\Http\Requests\Admin\Manage\ActivitydetailRequest;

class ActivitydetailController extends Controller
{
    public function index(ActivityDetailService $activitydetailService)
    {
        try {
            $result = $activitydetailService->getAll();
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