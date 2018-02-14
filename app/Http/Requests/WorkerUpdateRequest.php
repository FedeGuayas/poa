<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class WorkerUpdateRequest extends Request
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
            'area_id'=>'required',
            'dep_id'=>'required',
            'nombres'=>'required|max:100',
            'apellidos'=>'required|max:100',
            'email'=>'required|email|max:60|unique:users,id,:id',
            'num_doc'=>'required',
        ];
    }
    public function messages()
    {
        return [
            'area_id.required'=>'Debe seleccionar un área para el trabajador',
            'dep_id.required'=>'Debe seleccionar el departamento del trabajador',
            'nombres.required'=>'El nombre del trabajador es obligatorio',
            'nombres.max'=>'El nombre del trabajador es demasiado extenso, no exceda los 100 caracteres',
            'apellidos.required'=>'Los apellidos del trabajador son obligatorios',
            'apellidos.max'=>'Los apellidos del trabajador son demasiado extensos, no exceda los 100 caracteres',
            'email.required'=>'Debe escribir un email para el trabajador, con este podrá acceder al sistema',
            'email.email'=>'El correo no tiene un formato válido',
            'email.max'=>'El correo es demasiado extenso, no exceda los 60 caracteres o informelo al administrador del sistema',
            'email.unique'=>'El correo ya se encuentra en uso, si existe algún error informelo al administrador del sistema',
            'num_doc.required'=>'El númerod de documento de identificación del trabajador es obligatorio',
        ];
    }
}
