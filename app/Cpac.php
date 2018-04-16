<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cpac extends Model
{
    const CPAC_ACTIVA ='A'; //se encuentra como archivo actual subido en la cpac
    const CPAC_INACTIVA ='I'; //se deshabilito el archivo

    protected $fillable = [
        'pac_id','partida','cpc','monto','certificado','status'
    ];

    public function pac()
    {
        return $this->belongsTo('App\Pac');
    }
}
