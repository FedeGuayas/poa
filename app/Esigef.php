<?php

/**
 *  Modelo del Historico anual del Esigef
 */
namespace App;

use Illuminate\Database\Eloquent\Model;

class Esigef extends Model
{
    //protected $table = 'esigefs';
    public $timestamps = false;

    protected $fillable = [
        'exercise_id','cod_programa', 'cod_actividad', 'cod_item', 'codificado', 'devengado','mes'
    ];
}
