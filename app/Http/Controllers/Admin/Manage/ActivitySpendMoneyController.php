<?php

namespace App\Http\Controllers\Admin\Manage;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Admin\Manage\ActivitySpendMoneyService;
use App\Http\Resources\HTTPCreatedResponse;
use App\Http\Resources\HTTPSuccessResponse;
use App\Http\Requests\Admin\Manage\ActivitySpendMoneyRequest;
use App\Trait\Utils;
use Ramsey\Uuid\Type\Integer;

class ActivitySpendMoneyController extends Controller
{

    use Utils;
    public function index(Request $request, ActivitySpendMoneyService $activityService)
    {
        try {
            $perPage = $request->query('perpage', 10);
            $result = $activityService->getAll($perPage);
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


    public function getbyidactivity(string $id, ActivitySpendMoneyService $activityService)
    {
        try {
            $result = $activityService->getidactivity($id);
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

    public function show(string $id, ActivitySpendMoneyService $activityService)
    {
        try {
            $result = $activityService->show($id);
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

    public function update(ActivitySpendMoneyRequest $request, ActivitySpendMoneyService $projectService, string $id)
    {
        try {
            // $validated = $request->validate([
            //     'name_spendmoney' => 'required|string|max:255',
            //     'id_unit'         => 'required|integer', // ปรับ rule ตามจริง
            // ], [
            //     'name_spendmoney.required' => 'กรุณาระบุชื่อรายการค่าใช้จ่าย',
            //     'id_unit.required'         => 'กรุณาระบุหน่วยนับ',
            // ]);

            // $name_spendmoney = $request->name_spendmoney;
            // $id_unit = $request->id_unit;
             $activityDTO = $this->activitySpendMoneyRequestToActivitySpendDTO($request);
            // dd($studentCourseDTO);
            $result = $projectService->update($id, $activityDTO);
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

    public function destroy(ActivitySpendMoneyService $activityDetailService, string $id)
    {
        try {

            // $id = $request->id;
            $result = $activityDetailService->delete($id);
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
    public function store(ActivitySpendMoneyRequest $request, ActivitySpendMoneyService $activityService)
    {
        try {
            $activityDTO = $this->activitySpendMoneyRequestToActivitySpendDTO($request);
            // dd($activityDTO);
            $result = $activityService->store($activityDTO);
            // $result = $request->department_name;
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
}
