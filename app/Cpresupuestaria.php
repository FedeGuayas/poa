<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cpresupuestaria extends Model
{
    const CPRES_ACTIVA ='A'; //se encuentra como archivo actual subido en la cert presupuestaria
    const CPRES_INACTIVA ='I'; //se deshabilito el archivo

    protected $fillable = [
        'pac_id','user_upload','cod_cert_presup','cert_presup','status'
    ];

    protected $table='cpresupuestaria';

    public function pac()
    {
        return $this->belongsTo('App\Pac');
    }
}
