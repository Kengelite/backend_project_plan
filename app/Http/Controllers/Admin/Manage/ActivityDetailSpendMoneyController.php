<?php

namespace App\Http\Controllers\Admin\Manage;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Manage\ActivityDetailSpendMoneyRequest;
use Illuminate\Http\Request;
use App\Trait\Utils;
use App\Http\Resources\HTTPCreatedResponse;
use App\Http\Resources\HTTPSuccessResponse;
use App\Services\Admin\Manage\ActivityDetailSpendMoneyService;
use Illuminate\Validation\ValidationException;
class ActivityDetailSpendMoneyController extends Controller
{
    //
    use Utils;
    public function destroy(ActivityDetailSpendMoneyService $activityDetailService, string $id)
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
    public function store(ActivityDetailSpendMoneyRequest $request, ActivityDetailSpendMoneyService $activityService)
    {
        try {
            $activityDTO = $this->activityDetailSpendMoneyRequestToActivityDetaiSpendMoneylDTO($request);
            // dd($activityDTO);
            $result = $activityService->store($activityDTO);
            // $result = $request->department_name;
            $res = new HTTPCreatedResponse(['data' => $result]);
            return response()->json($res, \Illuminate\Http\Response::HTTP_CREATED);
        } catch (ValidationException $e) {              // ⬅️ จับก่อน \Exception เสมอ
            return response()->json([
                'message' => 'งบประมาณกิจกรรมไม่เพียงพอ',
                'errors'  => $e->errors(),
            ], \Illuminate\Http\Response::HTTP_UNPROCESSABLE_ENTITY);
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


    public function update(ActivityDetailSpendMoneyRequest $request, string $id, ActivityDetailSpendMoneyService $activityService)
    {
        try {
            $studentCourseDTO = $this->activityDetailSpendMoneyRequestToActivityDetaiSpendMoneylDTO($request);
            // dd($studentCourseDTO);
            $result = $activityService->update($id, $studentCourseDTO);
            $res = new HTTPCreatedResponse(['data' => $studentCourseDTO]);
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
