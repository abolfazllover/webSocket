<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Tymon\JWTAuth\Contracts\JWTSubject;


class User extends Model implements JWTSubject
{

    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'password',
        'token',
        'is_online',
    ];

    public function getJWTIdentifier()
    {
      return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
       return [
           'name'=>$this->name,
           'id'=>$this->id,
       ];
    }
}
