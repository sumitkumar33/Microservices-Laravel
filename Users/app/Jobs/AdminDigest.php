<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class AdminDigest implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    private $email;
    private $count;
    private $name;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($dump, $count)
    {
        $this->name = $dump['name'];
        $this->email = $dump['email'];
        $this->count = $count;
    }

    /**
     * Execute the job.
     * Send daily report to all administrators.
     * @return void
     */
    public function handle()
    {
        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'count' => $this->count,
        ];
        Http::post('http://notifications.myproject.com/api/DailyDigest', $data);
    }
}
