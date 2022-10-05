<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class AccountApprove implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    private $data;
    private $id;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($dump, $id)
    {
        $this->data = $dump;
        $this->id = $id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //Once the student/teacher account has been approved by the admin, trigger a mail to the
        //respective user.
        //Create in app notifications for above 2 cases.
        Http::post('http://notifications.myproject.com/api/notifications/notify/'.$this->id,
                $this->data);
        $data2 = [
            'name' => $this->data['name'],
            'email' => $this->data['email'],
            'admin_name' => $this->data['admin_name'],
        ];
        Http::post('http://notifications.myproject.com/api/approveMail', $data2);
    }
}
