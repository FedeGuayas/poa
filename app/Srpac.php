<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Srpac extends Model
{
    const SRPAC_ACTIVA ='A'; //se encuentra como archivo actual subido en la srpac
    const SRPAC_INACTIVA ='I'; //se deshabilito el archivo

    protected $fillable = [
        'pac_id','cod_item_ini','cpc_ini','tipo_compra_ini','concepto_ini','presupuesto_ini','cod_item_fin','cpc_fin','tipo_compra_fin','concepto_fin','presupuesto_fin','solicitud_file','notas','user_sol_id','user_aprueba_id','status'
    ];

    public function pac()
    {
        return $this->belongsTo('App\Pac');
    }

    public function srpac_destino()
    {
        return $this->hasMany('App\SrpacDestino');
    }
}
