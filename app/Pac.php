<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pac extends Model
{

    const PERMITIR_REFORMAR_PAC='1';
    const NO_REFORMAR_PAC='0';

    protected $fillable=[
        'area_item_id','trabajador_id','cod_item','item','presupuesto','devengado','disponible','mes','reform'
        
    ];

    public function esRoformable()
    {
        return $this->reform=Pac::PERMITIR_REFORMAR_PAC;
    }
    
    public function area_item()
    {
        return $this->belongsTo('App\AreaItem');
    }

    public function worker()
    {
        return $this->belongsTo('App\Worker');
    }

    public function detalles()
    {
        return $this->hasMany('App\Detalle');
    }

    public function cpacs()
    {
        return $this->hasMany('App\Cpac');
    }

}
