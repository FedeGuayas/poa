<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReformType extends Model
{
    protected $table='reform_type';

    public function reformas()
    {
        return $this->hasMany('App\Reforma');
    }

}
