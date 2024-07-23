<?php

namespace App\Jobs;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Mail;
use Config;

class SendMail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $requestData;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($requestData)
    {
        $this->requestData = $requestData;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::info('send mail Job processing started.');
        $this->que_send_mail($this->requestData);
        Log::info('send mail Job processing completed.');
    }

    public function que_send_mail($requestData){

        $to             = $requestData['email'];
        $fullName       = $requestData['name'];
        $subject        = $requestData['subject'];
        $messageBody    = $requestData['messageBody'];
        $from           = Config::get("Site.from_email");
        $files          = $requestData['files'] ?? false;
        $attachmentName = $requestData['attachment'] ?? '';
        $path           = $requestData['path'] ?? '';

        $data = array();
        $data['to'] = $to;
        $data['from'] = $from;
        $data['fullName'] = $fullName;
        $data['subject'] = $subject;
        $data['filepath'] = $path;
        $data['attachmentName'] = $attachmentName;
        try {
            if ($files === false) {
                Log::info('-------------------------------------Email here1');

                Mail::send('emails.template', array('messageBody' => $messageBody), function ($message) use ($data) {
                    $message->to($data['to'], $data['fullName'])->from($data['from'])->subject($data['subject']);
                    Log::info('-------------------------------------Email here2');
                });
            } else {
                Log::info('-------------------------------------Email here3');
                if ($attachmentName != '') {
                    Log::info('-------------------------------------Email here4');
                    Mail::send('emails.template', array('messageBody' => $messageBody), function ($message) use ($data) {
                        Log::info('-------------------------------------Email here4');
                        $message->to($data['to'], $data['fullName'])->from($data['from'])->subject($data['subject'])->attach($data['filepath'], array('as' => $data['attachmentName']));
                    });
                } else {
                    Log::info('-------------------------------------Email here5');
                    Mail::send('emails.template', array('messageBody' => $messageBody), function ($message) use ($data) {
                        Log::info('-------------------------------------Email here6');
                        $message->to($data['to'], $data['fullName'])->from($data['from'])->subject($data['subject'])->attach($data['filepath']);
                    });
                }
                Log::info('-------------------------------------Email here7');
            }
        
            Log::info('-------------------------------------Email sent successfully!');
        } catch (\Exception $e) {
            Log::error('-------------------------------------Error sending email: ' . $e->getMessage());
        }
        
        DB::table('email_logs')->insert(
            array(
                'email_to' => $data['to'],
                'email_from' => $from,
                'subject' => $data['subject'],
                'message' => $messageBody,
                'created_at' => DB::raw('NOW()'),
            )
        );
	}
}
