<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Informe extends Model
{
    protected $fillable = [
        'codificacion','numero'
    ];

    public function reformas()
    {
        return $this->hasMany('App\Reforma');
    }

}
