<?php

namespace App\Jobs;

use App\Http\Controllers\Api\EmailController;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class EmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $email;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($email_info)
    {
        $this->email = $email_info;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if (!env('SEND_LIFE_EMAIL')) {
            $this->email['body'] .= "<br>" . 'Attached Emails ' . \implode(' | ', $this->email['emails']);
            $this->email['emails'] = explode(',', env('TEST_EMAILS'));
        }

        \Log::info(' ====================================================================================================== ');
        \Log::info($this->email['subject']);
        \Log::info($this->email['body']);
        \Log::info(\implode(' | ', $this->email['emails']));
        \Log::info(' ====================================================================================================== ');

        if (env('APP_ENV') != 'local') {
            (new EmailController())->send($this->email['title'],  $this->email['body'],  $this->email['subject'],  $this->email['emails'],  $this->email['cc'],  $this->email['entity_id'],  $this->email['entity_type'], $this->email['bcc']);
        }
    }
}
