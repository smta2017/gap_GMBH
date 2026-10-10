<?php

namespace App\Helper;

use App\Http\Controllers\Api\EmailController;
use App\Http\Controllers\Api\NotificationController;
use App\Jobs\EmailJob;
use App\Models\Company;
use App\Models\CompanyType;
use App\Models\Request as ModelsRequest;
use App\Models\RequestProductTeeTime;
use App\Models\TeeTime;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TeeTimeNotificationsHelper
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
    private $teeTime;
    private $teeTimeProduct;
    private $ModelsRequest;

    private $emailTag;
    private $lang;


    public function __construct(Request $request, RequestProductTeeTime $teeTime)
    {
        $this->request = $request;
        $this->teeTime = $teeTime;
        $this->ModelsRequest = $this->teeTime->requestProduct->destination->request;
        $this->teeTimeProduct = $this->teeTime->requestProduct;

        $this->entity_id = $this->teeTime->id;
        $this->entity_type = 'App\Models\RequestProductTeeTime';

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
        Log::info('==== ' . $this->emailTag  . ' ======= ' . $this->ModelsRequest->id . ' ============== ' . $this->subject . ' =======================');
        Log::info('TO  : ' . $attatched_emails);
        Log::info('body    : ' . $this->body);
        Log::info('emails  : ' . \implode(' | ', $this->emails));
        Log::info('BCC  : ' . env('BCC_EMAIL'));
        Log::info('=======================================================================================================================================');

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

    public function handelTTimeNotify()
    {
        if ($this->request->status_id == 4 && $this->teeTime->is_parent) {
            $this->teeTimeOriginalConfirmed($this->lang);
        }

        if ($this->request->status_id == 4 && !$this->teeTime->is_parent) {
            $this->AlternativeConfirmed($this->lang);
        }

        if ($this->request->status_id == 3) {
            $this->teeTimeRejected($this->lang);
        }


        if ($this->request->store_alternative == 1) {
            $this->newAlternativeAdded($this->lang);
        }


        if ($this->request->send_reminder != '') {
            $this->teeTimeReminder($this->lang);
        }
    }

    private function teeTimeOriginalConfirmed($lang = 'de')
    {

        $this->emails = $this->getSenderEmails();

        $subject = [
            'en' => 'Teetime request - ID No. ' . $this->ModelsRequest->id . ' has been confirmed',
            'de' => 'Teetime Anfrage - ID Nr. ' . $this->ModelsRequest->id . ' wurde bestätigt'
        ];
        $body = [
            'en' => 'The destination partner has your tee time request with the no. ' . $this->ModelsRequest->id . ' confirmed. You can give the vouchers to the customer.',
            'de' => 'Der Zielgebietspartner hat Ihre Teetime Anfrage mit der No. ' . $this->ModelsRequest->id . ' bestätigt.  Sie können die Voucher dem Kunden aushändigen.'
        ];


        // Prepare Email
        $this->title = '';
        $this->bcc = explode(',', env('BCC_EMAIL'));
        // $this->emails = [$this->ModelsRequest->getAgencyOperatorsEmail()];
        // $this->emails = ($this->getSPHandlerEmail()) ? $this->getSPHandlerEmail() : [$this->ModelsRequest->getAgencyOperatorsEmail()];
        $this->subject = $subject[$lang];
        $this->body = $body[$lang];
        $this->emailTag = "Tee-Time-Original-Confirmed#4 ";
        // Send Mail
        $this->_send();

        // Send Notification
        $userCompanyIDs = $this->getSenderPushNotification();


        $notificationUsersData = User::whereHas('details', function ($query) use ($userCompanyIDs) {
            $query->whereIn('company_id', $userCompanyIDs);
        })->get();


        if (count($notificationUsersData) > 0) (new NotificationController())->sendWebNotificationUsers($this->subject, $this->body, $notificationUsersData);
    }

    private function teeTimeRejected($lang = 'de')
    {
        // Send Email to TA/GG/Client

        if (Helpers::TTUpdateorType($this->teeTime) == 'agancy') {

            $subject = [
                'en' => 'Teetime request no. ' . $this->ModelsRequest->id . ' has been changed',
                'de' => 'Opps! | Req.ID| Suggested Alternative TT #ID '. $this->teeTime->id .'Rejected'
            ];
            $body = [
                'en' => 'Thank you for your inquiry. Unfortunately, Golfclub ' . $this->ModelsRequest->id . ' was unable to confirm your tee time request (overseed, times fully booked, etc.).',
                'de' => 'The sp could not confirm the suggested alternative for the request no. ' . $this->ModelsRequest->id . ' .!'
            ];
        } elseif (Helpers::TTUpdateorType($this->teeTime) == 's_provider') {

            $subject = [
                'en' => 'Teetime request no. ' . $this->ModelsRequest->id . ' has been changed',
                'de' => 'Teetime Anfrage Nr. ' . $this->ModelsRequest->id . ' wurde geändert'
            ];
            $body = [
                'en' => 'Thank you for your inquiry. Unfortunately, Golfclub ' . $this->ModelsRequest->id . ' was unable to confirm your tee time request (overseed, times fully booked, etc.).',
                'de' => 'Danke für Ihre Anfrage. Der Golfclub hat Ihre Teetime Anfrage leider nicht bestätigen können (overseed, Zeiten ausgebucht, etc.)  .'
            ];
        }
        $this->emails = $this->getSenderEmails();


        // Prepare Email
        $this->title = '';
        $this->bcc = explode(',', env('BCC_EMAIL'));
        // $this->emails = [$this->ModelsRequest->getAgencyOperatorsEmail()];
        // $this->emails = ($this->getSPHandlerEmail()) ? $this->getSPHandlerEmail() : [$this->ModelsRequest->getAgencyOperatorsEmail()];
        $this->subject = $subject[$lang];
        $this->body = $body[$lang];
        $this->emailTag = "Tee-Time-Rejected#6 ";
        // Send Mail
        $this->_send();

        // Send Notification
        $userCompanyIDs = $this->getSenderPushNotification();

        $notificationUsersData = User::whereHas('details', function ($query) use ($userCompanyIDs) {
            $query->whereIn('company_id', $userCompanyIDs);
        })->get();

        if (count($notificationUsersData) > 0) (new NotificationController())->sendWebNotificationUsers($this->subject, $this->body, $notificationUsersData);
    }


    private function AlternativeConfirmed($lang = 'de')
    {
        $this->emails = $this->getSenderEmails();

        $subject = [
            'en' => 'Teetime request - ID No. ' . $this->ModelsRequest->id . ' has been confirmed',
            'de' => 'Teetime Anfrage - ID Nr. ' . $this->ModelsRequest->id . ' wurde bestätigt'
        ];
        $body = [
            'en' => 'The destination partner has your tee time request with the no. ' . $this->ModelsRequest->id . ' confirmed. You can give the vouchers to the customer.',
            'de' => 'Der Zielgebietspartner hat Ihre Teetime Anfrage mit der No. ' . $this->ModelsRequest->id . ' bestätigt.  Sie können die Voucher dem Kunden aushändigen.'
        ];
        // $dp =  $this->ModelsRequest->getAgencyOperatorsEmail();
        // $dp2 =  $this->getSPHandlerEmail();
        // Prepare Email
        $this->title = '';
        $this->bcc = explode(',', env('BCC_EMAIL'));
        // $this->emails = [$this->ModelsRequest->getAgencyOperatorsEmail()];
        // $this->emails = ($this->getSPHandlerEmail()) ? $this->getSPHandlerEmail() : [$this->ModelsRequest->getAgencyOperatorsEmail()];
        $this->subject = $subject[$lang];
        $this->body = $body[$lang];
        $this->emailTag = "Tee-Time-Alternative-Confirmed#4 ";
        // Send Mail
        $this->_send();

        // Send Notification
        $userCompanyIDs = $this->getSenderPushNotification();


        $notificationUsersData = User::whereHas('details', function ($query) use ($userCompanyIDs) {
            $query->whereIn('company_id', $userCompanyIDs);
        })->get();


        if (count($notificationUsersData) > 0) (new NotificationController())->sendWebNotificationUsers($this->subject, $this->body, $notificationUsersData);
    }



    public function newAlternativeAdded($lang = 'de')
    {


        if (Helpers::TTUpdateorType($this->teeTime) == 'agancy') {
            $subject = [
                'en' => 'Teetime request - ID No. ' . $this->ModelsRequest->id . ' has been confirmed',
                'de' => 'New alternative teetime request from Golfglobe TUI: ID ' . $this->ModelsRequest->id . '.'
            ];

            $body = [
                'en' => 'The destination partner has your tee time request with the no. ' . $this->ModelsRequest->id . ' confirmed. You can give the vouchers to the customer.',
                'de' => 'You have got a new request from Golfglobe TUI: ID ' . $this->ModelsRequest->id . ' .   Please take actions (confirm, delete or suggest alternatives). Klick on the link below and you will get directly to the request. Thank you!'
            ];
        } elseif (Helpers::TTUpdateorType($this->teeTime) == 's_provider') {

            $subject = [
                'en' => 'Teetime request - ID No. ' . $this->ModelsRequest->id . ' has been confirmed',
                'de' => 'Teetime Anfrage Nr. ' . $this->ModelsRequest->id . ' wurde geändert'
            ];

            $body = [
                'en' => 'The destination partner has your tee time request with the no. ' . $this->ModelsRequest->id . ' confirmed. You can give the vouchers to the customer.',
                'de' => 'Danke für Ihre Anfrage. Der Golfclub ' . $this->ModelsRequest->id . ' hat  neue Alternativmöglichkeiten angeboten, die von Ihrer Seite bestätigt werden müssen. '
            ];
        } elseif (Helpers::TTUpdateorType($this->teeTime) == 'golf_globe') {
          return;
        }


        $this->emails = $this->getSenderEmails();

        // Send Email to TA/GG/Client

        // Prepare Email
        $this->title = '';
        $this->bcc = explode(',', env('BCC_EMAIL'));
        // $this->emails = [$this->ModelsRequest->getAgencyOperatorsEmail()];
        // $this->emails = ($this->getSPHandlerEmail()) ? $this->getSPHandlerEmail() : [$this->ModelsRequest->getAgencyOperatorsEmail()];
        $this->subject = $subject[$lang];
        $this->body = $body[$lang];
        $this->emailTag = "New-Alternative-Added#4 ";
        // Send Mail
        $this->_send();

        // Send Notification

        $userCompanyIDs = $this->getSenderPushNotification();


        $notificationUsersData = User::whereHas('details', function ($query) use ($userCompanyIDs) {
            $query->whereIn('company_id', $userCompanyIDs);
        })->get();


        if (count($notificationUsersData) > 0) (new NotificationController())->sendWebNotificationUsers($this->subject, $this->body, $notificationUsersData);
    }


    public function teeTimeReminder($lang = 'de')
    {

        $reminder_val = "5 Days";
        $reminder_num = "Last";

        if ($this->request->send_reminder == '48h') {
            $reminder_val =  "48 Hour";
            $reminder_num =  "Second";
        }

        $subject = [
            'en' => 'Reminder | ' . $reminder_val . ' Passed | Req. ' . $this->ModelsRequest->id . ' | TT # ' . $this->teeTime->id,
            'de' => 'Reminder | ' . $reminder_val . ' Passed | Req. ' . $this->ModelsRequest->id . ' | TT # ' . $this->teeTime->id
        ];

        $body = [
            'en' => $reminder_num . ' Reminder: Please take actions: confirm, cancel or propose an alternative for the request No. ' . $this->ModelsRequest->id . '. Thank you in advance for your time and help.',
            'de' => $reminder_num . ' Reminder: Please take actions: confirm, cancel or propose an alternative for the request No. ' . $this->ModelsRequest->id . '. Thank you in advance for your time and help.'
        ];

        $this->title = '';
        $this->bcc = explode(',', env('BCC_EMAIL'));
        $this->subject = $subject[$lang];
        $this->body = $body[$lang];
        $this->emailTag = 'Reminder | ' . $reminder_val;
        // if ($this->ModelsRequest) {

        if (in_array($this->teeTimeProduct->service_handler_type_id, ['4', '6'])) // Hotel or DMC Handler
        { // Send Mail To Service Handler Mail
            // Prepare Email

            // $rr= $this->teeTime->user->details->company->type;
            if ($this->teeTimeProduct->get_service_handler_info()) {
                $this->emails = ($this->teeTimeProduct->get_service_handler_info()->email != '') ? [$this->teeTimeProduct->get_service_handler_info()->email] : ['NO@EMAIL.FOUND'];
            }

            // Send Notification
            $userCompanyIDs = [];
            if ($this->teeTimeProduct->get_service_handler_info()) {
                $userCompanyIDs[] = $this->teeTimeProduct->get_service_handler_info()->id;
            }
            $notificationUsersData = User::whereHas('details', function ($query) use ($userCompanyIDs) {
                $query->whereIn('company_id', $userCompanyIDs);
            })->get();
        } elseif (in_array($this->teeTimeProduct->service_handler_type_id, ['3'])) {
            $this->emails = ($this->teeTime->golfcourse) ? [$this->teeTime->golfcourse->email] : [];

            // Send Notification
            $userCompanyIDs = [];
            if ($this->teeTime->golfcourse) {
                $userCompanyIDs[] = $this->teeTime->golfcourse->company_id;
            }
            $notificationUsersData = User::whereHas('details', function ($query) use ($userCompanyIDs) {
                $query->whereIn('company_id', $userCompanyIDs);
            })->get();
        }

        $emails = $this->emails;
        $email_data = ['title' => $this->title, 'body' => $this->body, 'subject' => $this->subject, 'emails' => $emails, 'cc' => null, 'entity_id' => $this->entity_id,  'entity_type' => $this->entity_type, 'bcc' => $this->bcc];
        if (\env('APP_ENV') == 'local') {
            if (count($notificationUsersData) > 0) {
                (new NotificationController())->sendWebNotificationUsers($this->subject, $this->body, $notificationUsersData);
            }
            $this->_send();
        } else {
            if (count($notificationUsersData) > 0) {
                (new NotificationController())->sendWebNotificationUsers($this->subject, $this->body, $notificationUsersData);
            }
            EmailJob::dispatch($email_data);
        }
    }










    private function getSenderEmails()
    {
        return Helpers::getSenderEmails($this->teeTime);
    }

    private function getSenderPushNotification()
    {
        return Helpers::getSenderPushNotification($this->teeTime);
    }

    private function getSPHandlerEmail()
    {
        return Helpers::getSPHandlerEmail($this->teeTime);
    }

    private function getSPHandlerIds()
    {
        return Helpers::getSPHandlerIds($this->teeTime);
    }
}
