<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SrpacDestino extends Model
{
    protected $table='srpac_destino';

    protected $fillable = [
        'srpac_id','pac_id','cod_item','cpc','tipo_compra','concepto','presupuesto'
    ];

    public function pac()
    {
        return $this->belongsTo('App\Pac');
    }

    public function srpac()
    {
        return $this->belongsTo('App\Srpac');
    }
}
