<?php

namespace App\Http\Controllers\Admin\Manage;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Manage\UserRequest;
use App\Http\Resources\HTTPCreatedResponse;
use Illuminate\Http\Request;
use App\Http\Resources\HTTPSuccessResponse;
use App\Services\Admin\Manage\UserService;
use App\Trait\Utils;

class UploadfileController extends Controller
{
    use Utils;


    public function update(UserRequest $request, string $id, UserService $userService)
    {
        try {
            $secretKey = $request->header('X-Secret-Key');
            if ($secretKey !== env('SECRET_KEY_SUPERADMIN')) {
                return response()->json([
                    'message' => 'Unauthorized: Invalid secret key',
                ], \Illuminate\Http\Response::HTTP_FORBIDDEN);
            }
            $userDTO = $this->userRequestToUserDTO($request);
            $result  = $userService->update($id, $userDTO);
            $res     = new HTTPSuccessResponse(['data' => $result]);
            return response()->json($res, \Illuminate\Http\Response::HTTP_OK);
        } catch (\App\Exceptions\CustomException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'errors'  => $e->getErrorDetails(),
            ], $e->getStatusCode());
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], \Illuminate\Http\Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


}
