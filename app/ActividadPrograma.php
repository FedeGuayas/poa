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
}
