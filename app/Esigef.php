<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Esigef extends Model
{
    protected $fillable = [
        'ejercicio','cod_programa', 'cod_actividad', 'cod_item', 'programa', 'actividad', 'item', 'area', 'codificado', 'devengado', 'planificado', 'extras', 'mes'
    ];
}
