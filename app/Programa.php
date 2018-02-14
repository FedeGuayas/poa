<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Programa extends Model
{
    protected $fillable = [
        'cod_programa','programa'
    ];

    public $timestamps=false;
    
    public function actividads(){
        return $this->belongsToMany('App\Actividad')->withPivot('cod_actividad');
    }
}
