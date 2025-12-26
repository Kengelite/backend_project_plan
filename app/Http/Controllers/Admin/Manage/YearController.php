<?php

namespace App\Http\Controllers\Admin\Manage;

use App\Http\Controllers\Controller;
// use App\Http\Requests\Admin\Manage\yearRequest;
use App\Http\Resources\HTTPSuccessResponse;
use App\Services\Admin\Manage\YearService;
use App\Trait\Utils;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\Manage\YearRequest;
use App\Http\Resources\HTTPCreatedResponse;


class YearController extends Controller
{
    use Utils;
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

    public function yearallnew(YearService $yearService, Request $request)
    {
        try {
            $id_year = $request->input('id_year');
            $result = $yearService->getYearAllNew($id_year);
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


    public function insertyearallnew(YearService $yearService, Request $request)
    {
        try {
            $id_year = $request->input('id_year');
            $data = $request->input('data_new_year');
            $result = $yearService->insertDatanewYear($data, $id_year);
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


    public function yearallnewokr(YearService $yearService, Request $request)
    {
        try {
            $id_year = $request->input('id_year');
            $result = $yearService->getYearAllNewokr($id_year);
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


    public function insertyearallnewokr(YearService $yearService, Request $request)
    {
        try {
            $id_year = $request->input('id_year');
            $data = $request->input('okr');
            $result = $yearService->insertDatanewYearOkr($data, $id_year);
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
    public function yealUser(YearService $yearService)
    {
        try {
            $result = $yearService->getAllYearUser();
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


    public function updatestatus(YearService $yearService, Request $request)
    {
        try {
            $id = $request->id;
            $result = $yearService->updateStatus($id);
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

    public function destroy(YearService $yearService, Request $request)
    {
        try {
            $id = $request->id;
            $result = $yearService->delete($id);
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


    public function store(YearRequest $request, YearService $yearService)
    {
        try {
            $studentCourseDTO = $this->yearRequestToYearDTO($request);
            $result = $yearService->store($studentCourseDTO);
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

    public function update(YearRequest $request, YearService $yearService, string $id)
    {
        try {
            $studentCourseDTO = $this->yearRequestToYearDTO($request);
            $result = $yearService->update($studentCourseDTO, $id);
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
