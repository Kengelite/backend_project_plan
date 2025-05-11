<?php

namespace App\Http\Controllers\Admin\Manage;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Resources\HTTPSuccessResponse;
use App\Services\Admin\Manage\ActivityDetailService;
use App\Trait\Utils;
use App\Http\Requests\Admin\Manage\ActivitydetailRequest;
use App\Models\ActivityDetail;

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

    public function activitydetailByIdactivity(ActivityDetailService $activitydetailService,Request $request)
    {

        try {
            $id_activity = $request->id_activity;
            $result = $activitydetailService->getByIDactivity($id_activity);
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

    public function activitydetailByIdactivityAdmin(ActivityDetailService $activitydetailService,Request $request)
    {

        try {
            $id_activity = $request->id_activity;
            $result = $activitydetailService->getByIDactivityAdmin($id_activity);
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
    public function updatestatusActivityDetail(ActivityDetailService $activitydetailService,Request $request)
    {

        try {
            $id_activitydetail = $request->id_activitydetail;
            $result = $activitydetailService->updateStatus($id_activitydetail);
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

    public function destroy(ActivityDetailService $activityDetailService, Request $request)
    {
        try {
            $id_activitydetail = $request->id_activitydetail;
            $result = $activityDetailService->delete($id_activitydetail);
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
