<?php

namespace App\Http\Controllers\Api;

use App\Helper\Helpers;
use App\Helper\TeeTimeNotificationsHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Notification;
use App\Http\Resources\NotificationResource;
use App\Jobs\EmailJob;
use App\Models\Request as ModelsRequest;
use App\Models\RequestProductTeeTime;
use Carbon\Carbon;
use DB;
use Spatie\Activitylog\Contracts\Activity;

class NotificationController extends Controller
{
    public function index()
    {
        $requestPagination = request()->input('pagination');
        $pagination = ($requestPagination && is_numeric($requestPagination)) ? $requestPagination : 10;

        $user = request()->user();

        $notifications = Notification::where('user_id', $user->id)->paginate($pagination);

        $notificationData = NotificationResource::collection($notifications);

        return response()->json([
            'status' => true,
            'notifications' => $notificationData->response()->getData()
        ]);
    }

    public function get_last()
    {
        $requestNumber = request()->input('number');
        $number = ($requestNumber && is_numeric($requestNumber)) ? $requestNumber : 5;


        $user = request()->user();

        $notifications = Notification::where('user_id', $user->id)->orderBy('id', 'DESC')->take($number)->get();

        $notSeenCount = Notification::where('user_id', $user->id)->where('seen', '0')->count();

        $notificationData = NotificationResource::collection($notifications);

        return response()->json([
            'status' => true,
            'notifications' => $notificationData,
            'not_seen_count' => $notSeenCount
        ]);
    }

    public function update_seen(Request $request)
    {
        $validated = $request->validate([

            'notifications' => 'array',
            'notifications.*' => 'exists:notifications,id',
        ]);

        try {
            DB::beginTransaction();


            if (is_array($request->notifications) && count($request->notifications) > 0) {
                $notifications = Notification::whereIn('id', $request->notifications)->get();
                foreach ($notifications as $notification) {
                    $notification->update(['seen' => 1]);
                }
            }

            DB::commit();

            return response()->json([
                'status' => true,
            ]);
        } catch (\PDOException $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }


    public function sendWebNotificationUsers($title, $body, $users)
    {
        $user  = request()->user();

        $notificationUsers = [];
        foreach ($users as $u) {
            \Illuminate\Support\Facades\Log::info('send-Notification ====' . $u->username . '====' . $u->id . '***' . $title . '***' . $body);

            Notification::create([
                'user_id' => $u->id,

                'title' => $title,
                'body' => $body,

                'created_by' => ($user) ? $user->id : 1
            ]);

            foreach ($u->deviceKeys as $key) {
                $notificationUsers[] = $key->device_key;
            }
        }

        if (count($notificationUsers) > 0) {
            return $this->sendWebNotification($title, $body, $notificationUsers);
        }
        return \false;
    }

    public function sendWebNotification($title, $body, $FcmToken)
    {
        $url = 'https://fcm.googleapis.com/fcm/send';
        // $FcmToken = User::whereNotNull('device_key')->pluck('device_key')->all();

        $serverKey = 'AAAA2KPfo4o:APA91bGuRjrVx4cLROySrGwKNy_GMP638ZnLRs6jU5907yj3ouX26ik7I1NGIGuh88uVLnh7p2A1J-4MIZwADHNxCwDqIJ4-xAFAF5KWN6Z-k-x9sD89JJShoeoa3Of8m_L5ken5sEud';

        $data = [
            "registration_ids" => $FcmToken,
            "notification" => [
                "title" => $title,
                "body" => $body,
            ]
        ];
        $encodedData = json_encode($data);

        $headers = [
            'Authorization:key=' . $serverKey,
            'Content-Type: application/json',
        ];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        // Disabling SSL Certificate support temporarly
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $encodedData);
        // Execute post
        $result = curl_exec($ch);
        if ($result === FALSE) {
            die('Curl failed: ' . curl_error($ch));
        }
        // Close connection
        curl_close($ch);
        // FCM response
        // dd($result);        
        return $result;
    }

    public function ttnotconfirm48()
    {
        $time_excute = Carbon::now()->subDay()->toDateTimeString();
        if (!env('SEND_LIFE_EMAIL')) {
            $time_excute = Carbon::now()->subMinutes(2)->toDateTimeString();
        }
        $requestProductTeeTimes = RequestProductTeeTime::parents()->where('status_id', RequestProductTeeTime::REDIRECTED)->where('created_at', '<=', $time_excute)->whereNull('not_confirm_mail')->get();
        // $requestProductTeeTimes= RequestProductTeeTime::with('activities')->whereHas('activities',function($q) use($time_excute){
        //     $q->whereJsonContains('properties->attributes->status_id' , 2);
        //     $q->havingRaw('max(created_at) <= ?',[$time_excute]);
        // })->whereNull('not_confirm_mail')->get();
        // return count($requestProductTeeTimes);
        foreach ($requestProductTeeTimes as $teetime) {
            $request = new \Illuminate\Http\Request;
            $request['send_reminder'] = '48h';
            (new TeeTimeNotificationsHelper($request, $teetime))->handelTTimeNotify();

            activity()->withoutLogs(function () use ($teetime) {
                $teetime->update(['not_confirm_mail' => Carbon::now()]);
            });
        }
    }


    public function ttnotconfirm5day()
    {

        $time_excute = Carbon::now()->subDay()->toDateTimeString();
        if (!env('SEND_LIFE_EMAIL')) {
            $time_excute = Carbon::now()->subMinutes(5)->toDateTimeString();
        }
        $requestProductTeeTimes = RequestProductTeeTime::parents()->where('status_id', RequestProductTeeTime::REDIRECTED)->where('created_at', '<=', $time_excute)->whereNull('not_confirm5d_mail')->get();
        // $requestProductTeeTimes= RequestProductTeeTime::with('activities')->whereHas('activities',function($q) use($time_excute){
        //     $q->whereJsonContains('properties->attributes->status_id' , 2);
        //     $q->havingRaw('max(created_at) <= ?',[$time_excute]);
        // })->whereNull('not_confirm_mail')->get();
        // return count($requestProductTeeTimes);
        foreach ($requestProductTeeTimes as $teetime) {
            $request = new \Illuminate\Http\Request;
            $request['send_reminder'] = '5d';
            (new TeeTimeNotificationsHelper($request, $teetime))->handelTTimeNotify();

            activity()->withoutLogs(function () use ($teetime) {
                $teetime->update(['not_confirm5d_mail' => Carbon::now()]);
            });
        }
    }


    public function requestnotsubmited10d()
    {
        $time_excute = Carbon::now()->subDays(10)->toDateTimeString();

        if (!env('SEND_LIFE_EMAIL')) {
            $time_excute = \Carbon\Carbon::now()->subMinutes(10)->toDateTimeString();
        }

        $ModelsRequest =  ModelsRequest::where('sub_status_id', ModelsRequest::InComplete)->where('created_at', '<=', $time_excute)->whereNull('not_submit_mail')->get();

        foreach ($ModelsRequest as $key => $ModelRequest) {


            $title = '';

            $body = [
                'You have created Request ID #' . $ModelRequest->id . ' but not completed it. It will be automatically deleted from the system in two days if you do not complete the request.',
                'Sie haben die Anfragen-ID Nr. ' . $ModelRequest->id . ' kreiert, aber nicht vollendet. Sie wird automatisch vom System in zwei Tagen gelöscht, sollten Sie die Anfrage nicht vervollständigen.'
            ];

            // Send Mail
            $bcc = explode(',', env('BCC_EMAIL'));

            $sub = [
                'Incomplete Teetime request ID no. ' . $ModelRequest->id . ' will be deleted in 2 days',
                'Unvollständige Teetime Anfrage ID-Nr. ' . $ModelRequest->id . ' wird in 2 Tagen gelöscht'
            ];

            $emails =  [$ModelRequest->getAgencyOperatorsEmail()];

            $emailBody = $body[1];
           

            // if (!env('SEND_LIFE_EMAIL')) {
            //     $emailBody .= "<br>" . 'Attached Emails #9 ' . \implode($emails);
            //     $emails = explode(',', env('TEST_EMAILS'));
            // }

            // Send Mail

            $title = '';
            $email_data = ['title' => $title, 'body' => $emailBody, 'subject' => $sub[1], 'emails' => $emails, 'cc' => null, 'entity_id' => $ModelRequest->id,  'entity_type' => 'App\Models\Request', 'bcc' => $bcc];

            EmailJob::dispatch($email_data);

            activity()->withoutLogs(function () use ($ModelRequest) {
                $ModelRequest->update(['not_submit_mail' => Carbon::now()]);
            });

            // Send Notification
            $userCompanyIDs = $ModelRequest->getAgencyOperatorsCompanyIds();

            $notificationUsersData = User::whereHas('details', function ($query) use ($userCompanyIDs) {
                $query->whereIn('company_id', $userCompanyIDs);
            })->get();

            if (count($notificationUsersData) > 0) (new NotificationController())->sendWebNotificationUsers($sub[1], $body[1], $notificationUsersData);        }
    }

    public function sendPushNotification($userCompanyIDs = [], $title, $body)
    {
        $notificationUsersData = User::whereHas('details', function ($query) use ($userCompanyIDs) {
            $query->whereIn('company_id', $userCompanyIDs);
        })->get();

        if (count($notificationUsersData) > 0) {
            (new NotificationController())->sendWebNotificationUsers($title, $body, $notificationUsersData);
        }
    }
}
