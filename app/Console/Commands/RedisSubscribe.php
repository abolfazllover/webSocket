<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;

class RedisSubscribe extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'redis:subscribe';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
      Redis::subscribe('server_message',function (String $message){
          $data=json_decode($message,true);

          switch ($data['action']){
              case 'chaneStateUser' : $this->chaneStateUser($data['token'],boolval($data['is_online']));
          }

          Storage::write('testMessage.txt',$message);
      });
    }

    function chaneStateUser($token,$is_online){
        $user= User::where('token',$token)->first();
        $user->update(['is_online'=>$is_online]);
        $result= Redis::publish('users_status',json_encode(['id'=>$user->id,'is_online'=>$is_online]));
    }
}
