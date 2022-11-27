<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

class Members extends Authenticatable implements JWTSubject
{ 
    use HasFactory, HasApiTokens;
    protected $table = 'members';
    protected $fillable = [
      'name',
      'username',
      'email',
      'password',
    ];

    protected $hidden = [
      'password',
      'remember_token',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

}
