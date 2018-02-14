<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class ItemStoreRequest extends Request
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
            'programa' => 'required',
            'actividad'=> 'required',
            'codigo'=> 'required',
            'item'=> 'required',
            'presupuesto'=> 'required|numeric',
        ];
    }
    public function messages()
    {
        return [
            'programa.required' => 'Debe seleccionar el programa',
            'actividad.required' => 'Debe seleccionar la actividad',
            'codigo.required' => 'El código del item es obligatorio',
            'item.required' => 'El nombre del item es obligatorio',
            'presupuesto.required' => 'Debe asignar un presupuesto para el item',
            'presupuesto.numeric' => 'El presupuesto debe ser un número válido',
        ];
    }
}
