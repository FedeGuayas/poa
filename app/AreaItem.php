<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AreaItem extends Model
{
    const INCLUSION_YES='1'; //identificar si es una inclusion o no
    const INCLUSION_NO='0';


    protected $table='area_item';

    protected $fillable=[
        'item_id','area_id', 'monto','mes','inclusion'
    ];

    public function pacs()
    {
        return $this->hasMany('App\Pac');
    }

    public function reformas()
    {
        return $this->hasMany('App\Reforma');
    }

    public function item()
    {
        return $this->belongsTo('App\Item');
    }

    public function area()
    {
        return $this->belongsTo('App\Area');
    }

    public function month()
    {
        return $this->belongsTo('App\Month','mes','id');
    }
}
