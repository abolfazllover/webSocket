<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatModel extends Model
{
    protected $table='chat';
    protected $fillable=['from_id','to_id','message'];

    function from(){
        return $this->belongsTo(User::class,'from_id')->first();
    }

    function to(){
        return $this->belongsTo(User::class,'to_id')->first();
    }

    function token_from(){
        return $this->from()->token;
    }
    function token_to(){
        return $this->to()->token;
    }
}
