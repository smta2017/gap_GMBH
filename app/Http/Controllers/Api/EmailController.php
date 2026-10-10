<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Mail;
use App\Models\EmailLog;
use App\Models\EmailLogEmail;

class EmailController extends Controller
{
    public function send($title, $body, $subject, $emails = null, $cc = null, $entity_id = null, $entity_type = null, $bcc = null)
    {
        $data = [
            'title' => $title,
            'body' => $body
        ];

        if(!$emails){ return false; }

        Mail::send('templates.emails.request-status-template', $data, function($message) use ($emails, $subject, $cc, $bcc)
        {    
            $message->to($emails)
                    ->from('info@golfglobe.com', 'GOLFGLOBE')
                    ->subject($subject);  

            if($cc && is_array($cc))
            {
                $message->cc($cc);
            }

            if($bcc && is_array($bcc))
            {
                $message->bcc($bcc);
            }
        });

        $emailLog = EmailLog::create([
            'subject_id' => $entity_id,
            'subject_type' => $entity_type,
            'subject' => $subject,
            'body' => $body
        ]);

        foreach($emails as $e)
        {
            EmailLogEmail::create([
                'email_log_id' => $emailLog->id,
                'email' => $e,
                'type' => '1'
            ]);
        }

        if(is_array($cc))
        {
            foreach($cc as $e)
            {
                EmailLogEmail::create([
                    'email_log_id' => $emailLog->id,
                    'email' => $e,
                    'type' => '2'
                ]);
            }
        }
    }
}
