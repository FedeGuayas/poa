<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{

    protected $fillable=[
        'actividad_programa_id','cod_programa', 'cod_actividad','cod_item','grupo_gasto','item','presupuesto','disponible'
    ];

    public function actividad_programa()
    {
        return $this->belongsTo('App\ActividadPrograma');
    }

    public function extras()
    {
        return $this->hasMany('App\Extra');
    }

    public function areas()
    {
        return $this->belongsToMany('App\Area')->withPivot('monto','mes');
    }
    
}
