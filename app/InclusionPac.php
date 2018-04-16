<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InclusionPac extends Model
{
    const INCLUSION_PAC_ACTIVA ='A'; //se encuentra como archivo actual subido en la inclusion_pac
    const INCLUSION_PAC_INACTIVA ='I'; //se deshabilito el archivo

    protected $table='inclusion_pac';

    protected $fillable = [
        'pac_id','cod_item','cpc','tipo_compra','concepto','presupuesto','inclusion_file','user_sol_id','user_aprueba_id','status'
    ];

    public function pac()
    {
        return $this->belongsTo('App\Pac');
    }
}
