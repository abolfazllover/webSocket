<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Redis;

Route::get('/', [\App\Http\Controllers\AuthController::class,'chatPage'])->name('home');

Route::get('/login', [\App\Http\Controllers\AuthController::class,'login'])->name('login');
Route::post('/login', [\App\Http\Controllers\AuthController::class,'login_post'])->name('login_post');

Route::get('/register', [\App\Http\Controllers\AuthController::class,'register'])->name('register');
Route::post('/register', [\App\Http\Controllers\AuthController::class,'register_post'])->name('register_post');


Route::post('/sendMessage', [\App\Http\Controllers\ChatController::class,'sendMessage'])->name('sendMessage');


Route::get('test', function (){

//   Redis::publish('test',json_encode(['salam'=>'sdsdsd']));
  $result= \Junges\Kafka\Facades\Kafka::publish()->onTopic('test-topic') ->withBodyKey('message', ['hello'=>'سلام رضا'])->send();
  dd($result);

});
