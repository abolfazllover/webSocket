<?php

namespace App\Console\Commands;

use App\Jobs\SendDataWebsocket;
use App\Models\ChatModel;
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
          Storage::append('testMessage.txt',$message);
          try {
              Storage::append('testMessage.txt',$message);
              $data=json_decode($message,true);


              switch ($data['action']){

                  case 'chaneStateUser' : $this->chaneStateUser($data['userID'],boolval($data['is_online']));break;
                  case 'changeChat' : $this->changeChat($data['fromID'],$data['userID']);break;
              }


          }catch (Exception $exception){
              Storage::write('error.txt',$exception->getMessage());
          }
      });
    }

    function chaneStateUser($userID,$is_online){
        if($userID==null){
            return;
        }
        $user= User::find($userID);
        $user->update(['is_online'=>$is_online]);
        SendDataWebsocket::dispatch('users_status',json_encode(['id'=>$user->id,'is_online'=>$is_online]));
    }

    function changeChat($from,$to){
        $messages = ChatModel::where(function ($q) use ($from, $to) {
            $q->where('from_id', $from)->where('to_id', $to);
        })
            ->orWhere(function ($q) use ($from, $to) {
                $q->where('from_id', $to)->where('to_id', $from);
            })
            ->orderBy('id')
            ->get();
        SendDataWebsocket::dispatch('update_messages',json_encode(['userID'=>$from,'messages'=>$messages->toArray()]));
    }
}
