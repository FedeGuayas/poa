<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Departamento extends Model
{
    public $timestamps=false;
    protected $fillable = [
        'departamento'
    ];

    public function area()
    {
        return $this->belongsTo('App\Area');
    }

    public function workers()
    {
        return $this->hasMany('App\Worker');
    }
}
