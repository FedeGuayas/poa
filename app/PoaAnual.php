<?php

/**
 * Historico anual del POA, esigef item extras area_item area mes
 */

namespace App;

use Illuminate\Database\Eloquent\Model;

class PoaAnual extends Model
{
    protected $fillable = [
        'exercise_id', 'month_id', 'cod_programa', 'cod_actividad', 'cod_item', 'programa', 'actividad', 'item', 'presupuesto_plan', 'presupuesto_real','devengado', 'extras', 'area'
    ];

}
