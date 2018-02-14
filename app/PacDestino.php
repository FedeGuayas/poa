<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PacDestino extends Model
{
    protected $table='pac_destino';

    protected $fillable = [
        'reforma_id','pac_id','valor_dest'
    ];

    public function pac()
    {
        return $this->belongsTo('App\Pac');
    }

    public function reforma()
    {
        return $this->belongsTo('App\Reforma');
    }
}
