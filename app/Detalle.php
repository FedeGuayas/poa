<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Detalle extends Model
{
    protected $fillable = [
        'proveedor','num_factura','fecha_factura','fecha_entrega','importe','nota'
    ];

    public function pac()
    {
        return $this->belongsTo('App\Pac');
    }
}
