<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pac extends Model
{

    const NECESARIA_REFORMA_PAC='1'; //reform
    const NO_REFORMAR_PAC='0'; //reform=0
    const APROBADA_SRPAC='1'; //no necesario reforma, actualiza automatico cambios en cpc, tipo_compra y detalla
    const NO_APROBADA_SRPAC='0'; //srpac
    const PROCESO_INCLUSION_SI='1'; //identifica si el proceso es por una inclusion
    const PROCESO_INCLUSION_NO='0'; //

    const PROCESO_PAC='1'; //es un proceso pac, necesita CPAC y SRPAC
    const NO_PROCESO_PAC='0'; //no es un proceso pac, no necesita CPAC y SRPAC

    protected $fillable=[
        'area_item_id','worker_id','cod_item','item','presupuesto','devengado','disponible','liberado','mes','reform','srpac','tipo_compra','cpc','proceso_pac','inclusion'
    ];

    public function esProcesoPac()
    {
        return $this->proceso_pac=Pac::PROCESO_PAC;
    }

    public function noEsProcesoPac()
    {
        return $this->proceso_pac=Pac::NO_PROCESO_PAC;
    }
    
    public function area_item()
    {
        return $this->belongsTo('App\AreaItem');
    }

    public function meses()
    {
        return $this->belongsTo('App\Month','mes');
    }

    public function worker()
    {
        return $this->belongsTo('App\Worker');
    }

    public function detalles()
    {
        return $this->hasMany('App\Detalle');
    }

    //solicitud certificacion pac
    public function cpacs()
    {
        return $this->hasMany('App\Cpac');
    }

    //solicitud reforma pac
    public function srpacs()
    {
        return $this->hasMany('App\Srpac');
    }

    //solicitud inclusion pac
    public function inclusion_pacs()
    {
        return $this->hasMany('App\InclusionPac');
    }

    //archivo certificacion presupuestaria
    public function cpresupuestaria()
    {
        return $this->hasMany('App\Cpresupuestaria');
    }

}
