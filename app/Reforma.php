<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Reforma extends Model
{
    protected $fillable=[
        'area_item_id','user_id','monto_origen','estado'

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
    
}
