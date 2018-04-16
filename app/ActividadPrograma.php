<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ActividadPrograma extends Model
{
    protected $table='actividad_programa';
    
    public function items()
    {
        return $this->hasMany('App\Item');
    }

    public function actividad()
    {
        return $this->belongsTo('App\Actividad');
    }

    public function programa()
    {
        return $this->belongsTo('App\Programa');
    }
}
