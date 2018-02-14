<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class ActividadUpdateRequest extends Request
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
            'cod_actividad'=>'required|unique:actividads,id,:id',
            'actividad'=>'required'
        ];
    }

    public function messages()
    {
        return [
            'cod_actividad.required' => 'El código de la actividad es requerido',
            'cod_actividad.unique' => 'El código se encuentra registrado',
            'programa.required' => 'El nombre de la actividad es requerido',
        ];
    }
}
