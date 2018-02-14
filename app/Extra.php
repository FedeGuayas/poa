<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Extra extends Model
{
    protected $fillable = [
        'area_item_id','item_id','area_id','monto','mes'
    ];

    public function item()
    {
        return $this->belongsTo('App\Item');
    }

    public function area_item()
    {
        return $this->belongsTo('App\AreaItem');
    }

    public function area()
    {
        return $this->belongsTo('App\Area');
    }
        
}
