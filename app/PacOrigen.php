<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PacOrigen extends Model
{
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
