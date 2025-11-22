<?php

namespace App\Console\Commands;

use App\Jobs\SendDataWebsocket;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;
use mysql_xdevapi\Exception;

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
      ini_set('default_socket_timeout', -1);
      Redis::subscribe('server_message',function (String $message){
          try {
              Storage::append('testMessage.txt',$message);
              $data=json_decode($message,true);


              switch ($data['action']){
                  case 'chaneStateUser' : $this->chaneStateUser($data['token'],boolval($data['is_online']));
              }


          }catch (Exception $exception){
              Storage::write('error.txt',$exception->getMessage());
          }
      });
    }

    function chaneStateUser($token,$is_online){
        if($token==null){
            return;
        }
        $user= User::where('token',$token)->first();
        $user->update(['is_online'=>$is_online]);
        SendDataWebsocket::dispatch('users_status',json_encode(['id'=>$user->id,'is_online'=>$is_online]));
    }
}
