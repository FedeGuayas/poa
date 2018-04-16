<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PacOrigen extends Model
{
    const PACORIGEN_APROBADA='A';
    const PACORIGEN_PENDIENTE='P';

    protected $table='pac_origen';

    protected $fillable = [
        'reforma_id','pac_id','valor_orig'
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
