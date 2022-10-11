<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AccountApprove implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    private $id;
    private $data;
    //
    private $message;
    private $user_id;
    public $name;
    public $email;
    public $admin_name;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($dump, $id)
    {
        $this->data = $dump;
        $this->id = $id;
        //
        $this->message = $dump['message'];
        $this->user_id = $dump['user_id'];
        $this->name = $dump['name'];
        $this->email = $dump['email'];
        $this->admin_name = $dump['admin_name'];
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        /**
        * Once the student/teacher account has been approved by the admin, trigger a mail to the
        * respective user.
        * Create in app notifications for above 2 cases.
        **/
        Http::post(
            'http://notifications.myproject.com/api/notifications/notify/' . $this->id,
            $this->data
        );
        $data2 = [
            'name' => $this->name,
            'email' => $this->email,
            'admin_name' => $this->admin_name,
        ];
        Http::post('http://notifications.myproject.com/api/approveMail', $data2);
    }
}
