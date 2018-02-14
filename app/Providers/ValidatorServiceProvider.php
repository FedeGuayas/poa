<?php
/**
 * Created by PhpStorm.
 * User: halain
 * Date: 03-Nov-16
 * Time: 10:27:43 PM
 */
/*
 * Registrar la nueva clase PassValidator de validación en un Service Provider,
 * luego hay que registrar el Service Provider, en el archivo config/app.php
 * */

namespace App\Providers;

use App\PassValidator;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;


class ValidatorServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Validator::resolver(function($translator, $data, $rules, $messages)
        {
            return new PassValidator($translator, $data, $rules, $messages);
        });
    }

    public function register()
    {
    }
}