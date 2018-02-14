<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AreaItem extends Model
{
    protected $table='area_item';

    public function pacs()
    {
        return $this->hasMany('App\Pac');
    }

    public function reformas()
    {
        return $this->hasMany('App\Reforma');
    }

    public function extras()
    {
        return $this->hasMany('App\Extra');
    }

    public function item()
    {
        return $this->belongsTo('App\Item');
    }

    public function area()
    {
        return $this->belongsTo('App\Area');
    }
}
