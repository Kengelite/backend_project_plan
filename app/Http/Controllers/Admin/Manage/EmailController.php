<?php

namespace App\Http\Controllers\Admin\Manage;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Admin\Manage\ActivityService;
use Illuminate\Support\Facades\Mail;
use App\Mail\TestMail;
use App\Http\Resources\HTTPCreatedResponse;
use App\Http\Resources\HTTPSuccessResponse;
class EmailController extends Controller
{
    public function sendEmail(ActivityService $activityService, string $id, string $type)
    {
        try {
            $result = $activityService->getByIDforSendEmail($id);
            $update_send_email = $activityService->updateSendEmailByID($id);
            $result_user = $activityService->getUserByIDActivity($id);
            $details = [
                'footer' => 'ขอแสดงความนับถือ',
                'admin_name' => '[รัตติกร แทนเพชร]',
            ];

            if ($type === '1') {
                $details['title'] = 'แจ้งเตือน ความคืบหน้ากิจกรรม';
                $details['body'] = "ขอแจ้งติดตามความคืบหน้าของ{$result->name_activity} ขอความร่วมมือให้ดำเนินการกิจกรรมตามแผนที่กำหนดไว้
หากมีปัญหาหรือข้อขัดข้องประการใด กรุณาแจ้งให้ทราบ.";
            } else {
                $details['title'] = 'แจ้งเตือน รายงานผลการดำเนินงานกิจกรรม';
                $details['body'] = `ขอความร่วมมือจัดส่งรายงานผลการดำเนินงาน{$result->name_activity} เพื่อนำเสนอผู้เกี่ยวข้องต่อไป  กรุณาจัดส่งรายงานภายในวันที่ 30 กันยายน 2568 ผ่านระบบแผน e-plan
หากมีปัญหาหรือข้อขัดข้องประการใด กรุณาแจ้งให้ทราบ.`;
            }
            //                     $details = [
            //                         'title' => 'แจ้งเตือน  รายงานผล OKRs',
            //                         'body' => 'ขอความร่วมมือจัดส่งรายงานผล OKRs จำนวนหลักสูตรใหม่ตามความต้องการของสังคม  (สะสม) เพื่อนำเสนอผู้เกี่ยวข้องต่อไป  กรุณาจัดส่งรายงานภายในวันที่ 30 กันยายน 2568 ผ่านระบบแผน e-plan
            // หากมีปัญหาหรือข้อขัดข้องประการใด กรุณาแจ้งให้ทราบ.',
            //                         'footer' => 'ขอแสดงความนับถือ',
            //                         'admin_name' => '[รัตติกร แทนเพชร]'
            //                     ];

            foreach ($result_user as $item) {
                if ($item->user && $item->user->email) {
                    Mail::to($item->user->email)->send(new TestMail($details, $type, optional($item->user)->name));
                }
            }

            $res = new HTTPSuccessResponse(['data' => "Email sent successfully"]);
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

    public function testEmail(ActivityService $activityService, string $id, string $type)
    {
        try {
            $result = $activityService->getByIDforSendEmail($id);
            $res = new HTTPSuccessResponse(['data' => $result]);
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
