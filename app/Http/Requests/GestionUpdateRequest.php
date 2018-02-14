<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class GestionUpdateRequest extends Request
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
            'num_doc'=>'required',
            'proveedor'=>'required',
            'num_factura'=>'required',
            'fecha_factura'=>'required',
            'importe'=>'required|numeric'
        ];
    }
    public function messages()
    {
        return [
            'num_doc.required'=>'El RUC del proveedor es obligatorio.',
            'proveedor.required'=>'El proveedor es obligatorio.',
            'num_factura.required'=>'El número de la factura es obligatorio.',
            'fecha_factura.required'=>'La fecha de la factura es obligatorio.',
            'importe.required'=>'Debe asignar el valor ejecutado en la gestión',
            'importe.numeric'=>'El valor ejecutado debe ser un número'
        ];
    }
}
