<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Reforma extends Model
{
    const TIPO_INFORME_REF='reforma'; //tipo de informe tecnico Reforma, distintos items pero del mismo mes
    const TIPO_INFORME_REPROG='reprogramación'; //tipo de informe tecnico Reprogramacion, mismo item distintos meses
    const TIPO_INFORME_REF_REPROG='reforma/reprogramación'; //tipo de informe tecnico Reprogramacion, distintos items de distintos meses
    const COD_INFORME_MIN='MIN'; //codigo del informe tecnico MIN, movimiento poa entre actividades diferentes
    const COD_INFORME_MODIF='MODIF'; //codigo del informe tecnico MODIF, movimiento poa de la misma actividad
    const REFORMA_APROBADA='A';
    const REFORMA_PENDIENTE='P';

    protected $fillable=[
        'area_item_id','user_id','monto_orig','estado','reform_type_id','nota','tipo_informe','cod_informe','informe','num_min','num_modif'

    ];


    public function area_item()
    {
        return $this->belongsTo('App\AreaItem');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function pac_origen()
    {
        return $this->hasMany('App\PacOrigen');
    }

    public function pac_destino()
    {
        return $this->hasMany('App\PacDestino');
    }

    public function reform_type()
    {
        return $this->belongsTo('App\ReformType');
    }

    public function informe()
    {
        return $this->belongsTo('App\Informe');
    }
    
}
