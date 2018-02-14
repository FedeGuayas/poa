<?php
/**
 * Created by PhpStorm.
 * User: halain
 * Date: 03-Nov-16
 * Time: 10:22:11 PM
 */

/**
 * Clase para validar la contraseña anterior en el cambio de claves
 */

namespace App;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Validator as LaravelValidator;


class PassValidator extends LaravelValidator{

    public function validateCurrentPassword($attribute, $value, $parameters)
    {
        return Hash::check($value, Auth::user()->password);
    }
}
