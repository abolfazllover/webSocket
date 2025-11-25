<?php

namespace App\Http\Controllers;

use App\Models\ChatModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class RedisMessagerController extends Controller
{
    function new_message(ChatModel $chatModel){
        $chat=[
            'message' => $chatModel->message,
            'from_id'=>$chatModel->from_id,
            'to_id'=>$chatModel->to_id,
        ];
        Redis::publish('new_message',json_encode($chat));
    }
}
