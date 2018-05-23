<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Extra extends Model
{
    protected $fillable = [
        'item_id','area_id','monto','mes'
    ];

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
