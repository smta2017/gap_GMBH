<?php

namespace App\Helper;

use App\Http\Controllers\Api\EmailController;
use App\Http\Controllers\Api\NotificationController;
use App\Models\Company;
use App\Models\EmailLog;
use App\Models\Request as ModelsRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NotificationsHelper
{

    private $title;
    private $body;
    private $subject;
    private $emails = [];
    private $cc = null;
    private $entity_id = null;
    private $entity_type = '';
    private $bcc = null;

    private $request;
    private $ModelsRequest;

    private $emailTag;
    private $lang;


    public function __construct(Request $request, ModelsRequest $ModelsRequest)
    {
        $this->request = $request;
        $this->ModelsRequest = $ModelsRequest;

        $this->entity_id = $this->ModelsRequest->id;
        $this->entity_type = 'App\Models\Request';

        $this->lang = 'de';
    }

    public function _send()
    {
        $attatched_emails = \implode(' | ', $this->emails);
        if (!env('SEND_LIFE_EMAIL')) {
            $this->body .= "<br>" . 'Attached Emails ' . $this->emailTag . \implode(' | ', $this->emails);
            $this->emails = explode(',', env('TEST_EMAILS'));
        }

        Log::info('');
        Log::info('');
        Log::info('');
        Log::info('======== ' . $this->emailTag  . ' ======== ' . $this->request->id . ' ============== ' . $this->subject . ' =======================');
        Log::info('to  : ' . $attatched_emails);
        Log::info('body    : ' . $this->body);
        Log::info('emails  : ' . \implode(' | ', $this->emails));
        Log::info('BCC  : ' . env('BCC_EMAIL'));
        Log::info('======== ' . $this->emailTag  . ' ============================================================================================');

        EmailLog::create([
            'subject_id' => $this->entity_id,
            'subject_type' => $this->entity_type,
            'subject' => $this->subject,
            'body' => $this->body
        ]);

        if (env("APP_ENV") != 'local') {
            (new EmailController())->send(
                $this->title,
                $this->body,
                $this->subject,
                $this->emails,
                $this->cc,
                $this->entity_id,
                $this->entity_type,
                $this->bcc
            );
        }
    }

    public function handelRequestNotify()
    {
        if ($this->request->is_submit == 1 && $this->ModelsRequest->sub_status_id == 1) {
            $this->requestSubmitted($this->lang);
        }
        if ($this->request->is_approved == 1) {
            if ($this->ModelsRequest->sub_status_id == 5) {
                $this->requestApproved($this->lang);
            }
            if ($this->ModelsRequest->sub_status_id == 6) {
                $this->requestSysRedirected($this->lang);
            }
        }
    }

    private function requestSubmitted($lang = 'de')
    {
        // Send Email to TA/GG/Client
        $subject = [
            'en' => 'Teetime Request ID ' . $this->ModelsRequest->id . ' was created successfully',
            'de' => 'Teetime Anfrage ID ' . $this->ModelsRequest->id . ' wurde erfolgreich erstellt '
        ];
        $body = [
            'en' => 'Thanks. You have fully created Request ID #' . $this->ModelsRequest->id . ' and it will be automatically forwarded to the GG team. You will receive feedback via email from this system as soon as possible.',
            'de' => 'Danke. Sie haben die Anfragen-ID Nr. ' . $this->ModelsRequest->id . ' vollständig kreiert und diese wird automatisch an das GG-Team weitergeleitet. Sie erhalten so schnell wie möglich eine Rückmeldung per E-Mail aus diesem System.'
        ];
        // Prepare Email
        $this->title = '';
        $this->bcc = explode(',', env('BCC_EMAIL'));
        $this->emails = [$this->ModelsRequest->getAgencyOperatorsEmail(), $this->ModelsRequest->getGGEmail()];
        $this->subject = $subject[$lang];
        $this->body = $body[$lang];
        $this->emailTag = "Request-Submitted#2 ";

        // Send Mail
        $this->_send();

        // Send Notification
        $userCompanyIDs = array_merge($this->ModelsRequest->getAgencyOperatorsCompanyIds(), $this->ModelsRequest->getGGId());
        $notificationUsersData = User::whereHas('details', function ($query) use ($userCompanyIDs) {
            $query->whereIn('company_id', $userCompanyIDs);
        })->get();

        if (count($notificationUsersData) > 0) {
            (new NotificationController())->sendWebNotificationUsers($this->subject, $this->body, $notificationUsersData);
        }
    }

    private function requestApproved($lang = 'de')
    {
        // Send Email to TA/GG/Client
        $subject = [
            'en' => 'New tee time request from Golf Globe TUI: ID  ' . $this->ModelsRequest->id . '.',
            'de' => 'Teetime Anfrage-ID Nr. ' . $this->ModelsRequest->id . '  in Bearbeitung'
        ];

        $body = [
            'en' => 'You have got a new request from Golfglobe TUI: ID ' . $this->ModelsRequest->id . ' .   Please take actions (confirm, delete or suggest alternatives). Klick on the link below and you will get directly to the request. Thank you!',
            'de' => 'Ihre Anfrage No. ' . $this->ModelsRequest->id . ' wird von unserem Team (Golfglobe) bearbeitet. Sie erhalten so schnell wie möglich eine Rückmeldung per E-Mail aus diesem System.'
        ];

        // Prepare Email
        $this->title = '';
        $this->bcc = explode(',', env('BCC_EMAIL'));
        $this->emails = [$this->ModelsRequest->getAgencyOperatorsEmail()];
        $this->subject = $subject[$lang];
        $this->body = $body[$lang];
        $this->emailTag = "Request-Approved#3 ";

        // Send Mail
        $this->_send();

        // Send Notification
        $userCompanyIDs = $this->ModelsRequest->getAgencyOperatorsCompanyIds();
        $notificationUsersData = User::whereHas('details', function ($query) use ($userCompanyIDs) {
            $query->whereIn('company_id', $userCompanyIDs);
            $query->whereHas('company', function ($q) {
                $q->where('company_type_id', 2);
            });
        })->get();

        if (count($notificationUsersData) > 0) (new NotificationController())->sendWebNotificationUsers($this->subject, $this->body, $notificationUsersData);
    }

    private function requestSysRedirected($lang = 'de')
    { // Send Email to TA/GG/Client
        $subject = [
            'en' => 'New tee time request from Golf Globe TUI: ID  ' . $this->ModelsRequest->id . '.',
            'de' => 'New teetime request from Golfglobe TUI: ID ' . $this->ModelsRequest->id . '.'
        ];

        $body = [
            'en' => 'You have got a new request from Golfglobe TUI: ID ' . $this->ModelsRequest->id . '.   Please take actions (confirm, delete or suggest alternatives). Klick on the link below and you will get directly to the request. Thank you!',
            'de' => 'You have got a new request from Golfglobe TUI: ID ' . $this->ModelsRequest->id . '.   Please take actions (confirm, delete or suggest alternatives). Klick on the link below and you will get directly to the request. Thank you!'
        ];

        foreach ($this->ModelsRequest->destinations as $destination) {
            foreach ($destination->products as $product) {

                $this->title = '';
                $this->bcc = explode(',', env('BCC_EMAIL'));
                $this->subject = $subject[$lang];
                $this->body = $body[$lang];

                if (in_array($product->service_handler_type_id, ['4', '6'])) // Hotel or DMC Handler
                { // Send Mail To Service Handler Mail

                    // Prepare Email
                    if ($product->get_service_handler_info()) {
                        $this->emails = ($product->get_service_handler_info()->email != '') ? [$product->get_service_handler_info()->email] : ['NO@EMAIL.FOUND'];
                    }

                    $this->emailTag = "Request-Sys-Redirected|Hotel or DMC Handler#2-1 ";

                    // Send Mail
                    $this->_send();

                    // Send Notification
                    $userCompanyIDs = [];
                    if ($product->get_service_handler_info()) {
                        $userCompanyIDs[] = $product->get_service_handler_info()->id;
                        // execloude GG from array companies 
                        $fields = array_flip($userCompanyIDs);
                        unset($fields['1']);
                        $userCompanyIDs = array_flip($fields);
                    }

                    $notificationUsersData = User::whereHas('details', function ($query) use ($userCompanyIDs) {
                        $query->whereIn('company_id', $userCompanyIDs);
                    })->get();

                    if (count($notificationUsersData) > 0) {
                        (new NotificationController())->sendWebNotificationUsers($this->subject, $this->body, $notificationUsersData);
                    }
                } elseif (in_array($product->service_handler_type_id, ['3'])) {
                    foreach ($product->requestTeeTimes as $teeTime) {
                        // Send Mail To GolfCourse
                        $this->body = $body[$lang];
                        $this->emails = ($teeTime->golfcourse) ? [$teeTime->golfcourse->email] : [];
                        $this->emailTag = "Request-Sys-Redirected|GolfCourse-tee-times #2-2 ";

                        // Send Mail
                        $this->_send();

                        // Send Notification
                        $userCompanyIDs = [];
                        if ($teeTime->golfcourse) {
                            $userCompanyIDs[] = $teeTime->golfcourse->company_id;

                            // execloude GG from array companies 
                            $fields = array_flip($userCompanyIDs);
                            unset($fields['1']);
                            $userCompanyIDs = array_flip($fields);
                        }

                        $notificationUsersData = User::whereHas('details', function ($query) use ($userCompanyIDs) {
                            $query->whereIn('company_id', $userCompanyIDs);
                        })->get();

                        if (count($notificationUsersData) > 0) {
                            (new NotificationController())->sendWebNotificationUsers($this->subject, $this->body, $notificationUsersData);
                        }
                    }
                }
            }
        }
    }
}
