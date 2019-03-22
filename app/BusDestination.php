<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BusDestination extends Model
{
    public function bus()
    {
        return $this->belongsTo('App\Bus');
    }
}
