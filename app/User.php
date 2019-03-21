<?php

namespace App;

use App\Traits\Image;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements JWTSubject
{
    use Image, Notifiable;

    protected static $imagePath = '/app-img/users';
    protected static $imageColumn = 'photo';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 'last_name', 'email', 'gender', 'phone', 'photo', 'birthday', 'password', 
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [];

    public function getJWTIdentifier()
    {
      return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [
            'email' => $this->email,
            'first_name' => $this->first_name, 
            'last_name' => $this->last_name,
            'phone' => $this->phone,
            'birthday' => $this->birthday,
            'matric_no' => $this->matric_no,
            'gender' => $this->gender,
            'city' => $this->city,
            'country' => $this->country,
            'photo' => $this->imagePath(),
            'address' => $this->relationship_status
        ];
    }
}
