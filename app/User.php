<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Hash;
use Zizaco\Entrust\Traits\EntrustUserTrait;

class User extends Authenticatable
{
    /**
     * Este trait habilita las relaciones del modelo User con el modelo Role, adicionando los siguientes metodos
     * roles(), hasRole($name), can($permission), and ability($roles, $permissions, $options)
     */
    use EntrustUserTrait;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'worker:_id','name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function reformas()
    {
        return $this->hasMany('App\Reforma');
    }

    public function worker()
    {
        return $this->belongsTo('App\Worker');
    }

    //setear el password, ya no es necesario encriptar pass en controlador
    public function setPasswordAttribute($value)
    {
        if ( ! empty ($value))
        {
            $this->attributes['password'] = Hash::make($value);
        }
    }

}
