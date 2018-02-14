<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Actividad extends Model
{
//    protected $fillable=[
//      'cod_actividad','actividad'
//    ];
    
    public $timestamps=false;

    public function programas(){
        return $this->belongsToMany('App\Programa')->withPivot('cod_programa');
    }
}
