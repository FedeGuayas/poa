<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cpac extends Model
{
    protected $fillable = [
        'pac_id','partida','cpc','monto','certificado'
    ];

    public function pac()
    {
        return $this->belongsTo('App\Pac');
    }
}
