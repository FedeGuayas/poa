<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    public $timestamps=false;
    protected $fillable = [
        'area'
    ];
    
    public function departamentos()
    {
        return $this->hasMany('App\Departamento');
    }

    public function area_item()
    {
        return $this->hasMany('App\AreaItem');
    }

    public function extras()
    {
        return $this->hasMany('App\Extra');
    }
}
