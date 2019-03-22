<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Destination extends Model
{
    public function terminals()
    {
        return $this->hasMany('App\Terminal');
    }
}
