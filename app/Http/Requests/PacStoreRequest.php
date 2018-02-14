<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class PacStoreRequest extends Request
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
            'area_item_id'=>'required',
            'worker'=>'required',
            'cod_item'=>'required',
            'item'=>'required',
            'concepto'=>'required',
            'procedimiento'=>'required',
            'valor'=>'required|numeric',
            'mes'=>'required',
            'tipo_compra'=>'required'
        ];
    }
    public function messages()
    {
        return [
            'area_item_id.required'=>'El item y el área son campos obligatorios',
            'worker.required'=>'El trabajador es obligatorio, seleccione el respopnsable',
            'cod_item.required'=>'El código del item es obligatorio',
            'item.required'=>'El item es obligatorio',
            'concepto.required'=>'El concepto es obligatorio. Debe describir el proceso',
            'procedimiento.required'=>'El procedimiento es oligatorio. Seleccione el procedimiento',
            'valor.required'=>'Debe asignar el valor del proceso',
            'valor.numeric'=>'El valor del proceso debe ser un número',
            'mes.required'=>'El mes es obligatorio',
            'tipo_compra.required'=>'Seleccione el tipo de compra'
        ];
    }
}
