<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class ChangePasswordRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'password' => 'required|min:6|current_password',
            'password_new' => 'required|confirmed|min:6'
        ];
    }

    public function messages()
    {
        return [
            'password.required' => 'La contraseña anterior es requerida',
            'password.current_password' => 'La contraseña anterior no coincide',
            'password_new.required' => 'Debe escribir la nueva contraseña',
            'password_new.min' => 'La nueva contraseña debe tener al menos 6 caracteres',
            'password_new.confirmed' => 'Las contraseñas no coincide',

        ];
    }

}
