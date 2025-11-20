<?php

namespace App\Http\Controllers;

use App\Models\ChatModel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class ChatController extends Controller
{

    public $userSender;
    public $redisMessager;
    public function __construct(Request $request)
    {
        $this->userSender=User::where('token',$request->get('token'))->first();
        $this->redisMessager=new RedisMessagerController();
    }

    function sendMessage(Request $request){
        if ($this->userSender==null){
            return error_json('کاربری شما یافت نشد');
        }else{
            $data=$request->all();
            $data['from_id']=$this->userSender->id;
            $data['to_id']=$request->get('to');
            $chat=ChatModel::create($data);
            $this->redisMessager->new_message($chat);
            return  success_json('با موفقیت ارسال شد');
        }
    }
}
