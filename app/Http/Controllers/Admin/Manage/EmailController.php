<?php

namespace App\Http\Controllers\Admin\Manage;

use App\Http\Controllers\Controller;
use App\Services\Admin\Manage\ActivityService;
use App\Services\Admin\Manage\OkrService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;
use App\Mail\TestMail;

class EmailController extends Controller
{
    public function sendEmail(ActivityService $activityService, string $id, string $type)
    {
        try {
            $result = $activityService->getByIDforSendEmail($id);
            $result_user = $activityService->getUserByIDActivity($id);

            $details = [
                'footer' => 'ขอแสดงความนับถือ',
                'admin_name' => '[รัตติกร แทนเพชร]',
            ];

            if ($type === '1') {
                $details['title'] = 'แจ้งเตือน ความคืบหน้ากิจกรรม';
                $details['body'] = "ขอแจ้งติดตามความคืบหน้าของ{$result->name_activity} ขอความร่วมมือให้ดำเนินการกิจกรรมตามแผนที่กำหนดไว้
หากมีปัญหาหรือข้อขัดข้องประการใด กรุณาแจ้งให้ทราบ.";
            } elseif ($type === '2') {
                $details['title'] = 'แจ้งเตือน รายงานผลการดำเนินงานกิจกรรม';
                $details['body'] = "ขอความร่วมมือจัดส่งรายงานผลการดำเนินงาน{$result->name_activity} เพื่อนำเสนอผู้เกี่ยวข้องต่อไป กรุณาจัดส่งรายงานภายในวันที่ 30 กันยายน 2568 ผ่านระบบแผน e-plan
หากมีปัญหาหรือข้อขัดข้องประการใด กรุณาแจ้งให้ทราบ.";
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'ประเภทการแจ้งเตือนกิจกรรมไม่ถูกต้อง',
                ], Response::HTTP_BAD_REQUEST);
            }

            $sentCount = 0;

            foreach ($result_user as $item) {
                if ($item->user && $item->user->email) {
                    Mail::to($item->user->email)->send(
                        new TestMail($details, $type, optional($item->user)->name)
                    );

                    $sentCount++;
                }
            }

            if ($sentCount <= 0) {
                return response()->json([
                    'status' => false,
                    'message' => 'ไม่พบอีเมลผู้รับผิดชอบกิจกรรม',
                ], Response::HTTP_BAD_REQUEST);
            }

            $activityService->updateSendEmailByID($id);

            return response()->json([
                'status' => true,
                'message' => 'Email sent successfully',
                'data' => [
                    'activity_id' => $id,
                    'type' => $type,
                    'sent_count' => $sentCount,
                ],
            ], Response::HTTP_OK);
        } catch (\App\Exceptions\CustomException $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
                'errors' => $e->getErrorDetails(),
            ], $e->getStatusCode());
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function testEmail(ActivityService $activityService, string $id, string $type)
    {
        try {
            $result = $activityService->getByIDforSendEmail($id);

            return response()->json([
                'status' => true,
                'message' => 'success',
                'data' => $result,
            ], Response::HTTP_CREATED);
        } catch (\App\Exceptions\CustomException $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
                'errors' => $e->getErrorDetails(),
            ], $e->getStatusCode());
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function sendEmailOkr(OkrService $okrService, string $id, string $type)
    {
        try {
            $result = $okrService->getByIDforSendEmail($id);
            $result_user = $okrService->getUserByIDOkr($id);

            $details = [
                'footer' => 'ขอแสดงความนับถือ',
                'admin_name' => '[รัตติกร แทนเพชร]',
            ];

            if ($type === '1') {
                $details['title'] = 'แจ้งเตือน ความคิดเห็น OKR';
                $details['body'] = "ขอแจ้งติดตามความคิดเห็น OKR {$result->okr_name} ขอความร่วมมือให้ดำเนินการตรวจสอบหรือแสดงความคิดเห็นตามแผนที่กำหนดไว้
หากมีปัญหาหรือข้อขัดข้องประการใด กรุณาแจ้งให้ทราบ.";

                $mailType = 'okr_comment';
            } elseif ($type === '2') {
                $details['title'] = 'แจ้งเตือน รายงานผล OKR';
                $details['body'] = "ขอความร่วมมือจัดส่งรายงานผล OKR {$result->okr_name} เพื่อนำเสนอผู้เกี่ยวข้องต่อไป กรุณาจัดส่งรายงานภายในวันที่ 30 กันยายน 2568 ผ่านระบบแผน e-plan
หากมีปัญหาหรือข้อขัดข้องประการใด กรุณาแจ้งให้ทราบ.";

                $mailType = 'okr_report';
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'ประเภทการแจ้งเตือน OKR ไม่ถูกต้อง',
                ], Response::HTTP_BAD_REQUEST);
            }

            $sentCount = 0;

            foreach ($result_user as $item) {
                if ($item->user && $item->user->email) {
                    Mail::to($item->user->email)->send(
                        new TestMail($details, $mailType, optional($item->user)->name)
                    );

                    $sentCount++;
                }
            }

            if ($sentCount <= 0) {
                return response()->json([
                    'status' => false,
                    'message' => 'ไม่พบอีเมลผู้รับผิดชอบ OKR',
                ], Response::HTTP_BAD_REQUEST);
            }

            $okrService->updateSendEmailByID($id);

            return response()->json([
                'status' => true,
                'message' => 'Email sent successfully',
                'data' => [
                    'okr_id' => $id,
                    'type' => $type,
                    'mail_type' => $mailType,
                    'sent_count' => $sentCount,
                ],
            ], Response::HTTP_OK);
        } catch (\App\Exceptions\CustomException $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
                'errors' => $e->getErrorDetails(),
            ], $e->getStatusCode());
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}