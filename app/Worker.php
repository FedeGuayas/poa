<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Worker extends Model
{
    protected $fillable = [
        'departamento_id','nombres','apellidos','email','telefono'
    ];
    
    public function pacs()
    {
        return $this->hasMany('App\Pac');
    }

    public function departamento()
    {
        return $this->belongsTo('App\Departamento');
    }

    public function user()
    {
        return $this->hasOne('App\user');
    }
}
