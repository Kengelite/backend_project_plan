<?php

namespace App\Http\Controllers\Admin\Manage;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\HTTPCreatedResponse;
use App\Http\Resources\HTTPSuccessResponse;
use App\Services\Admin\Manage\OkrUserService;
class UserOkrController extends Controller
{

    public function update(Request $request, OkrUserService $projectService,string $id)
    {
        try {
            $id_user = $request->id_user;
            $result = $projectService->update($id, $id_user);
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
    public function store(Request $request, OkrUserService $projectService)
    {
        try {
            $id_user = $request->id_user;
            $object_project_Id = $request->id;
            $type = $request->type;
            $id_year = $request->id_year;
            $result = $projectService->store($id_user, $object_project_Id, $type, $id_year);
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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(OkrUserService $projectService, Request $request,string $id)
    {
        try {
            // $type = $request->type;
            $result = $projectService->delete($id);
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
