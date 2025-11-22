<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Redis;

class SendDataWebsocket implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public $chanel;
    public $message;
    public function __construct($_chanel,$_message)
    {
        $this->chanel=$_chanel;
        $this->message=$_message;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $result= Redis::publish($this->chanel,$this->message);
        echo $result;
    }
}
