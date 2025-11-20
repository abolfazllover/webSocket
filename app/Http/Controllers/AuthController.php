<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    function hasUser(){
        $user=User::find(getCookie('user'));
        return $user!=null;
    }

   function chatPage(Request  $request){
        if (!$this->hasUser()){
            return redirect()->route('login');
        }
        $user=User::find(getCookie('user'));

        return  view('welcome',[
            'user'=>$user,
            'users'=>User::where('id','!=',$user->id)->get(),
        ]);
   }

   function login(){
        return view('login');
   }

   function login_post(Request $request){
        $user=User::where('password',$request->get('password'))->where('email',$request->get('email'))->first();
        if ($user==null){
            return back()->withErrors('user and password invalid');
        }else{
            saveCookie('user',$user->id,120);
            return redirect()-> route('home');
        }
   }


   function register(){
       return view('register');
   }

   function register_post(Request $request){
      $id=  User::create(array_merge($request->all(),['token'=>Str::random(17)]));
      return back()->with('success','با موفقیت ایجاد شد');
   }
}
