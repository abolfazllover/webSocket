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
            'token_from'=>$chatModel->token_from(),
            'token_to'=>$chatModel->token_to(),
        ];
        Redis::publish('new_message',json_encode($chat));
    }
}
