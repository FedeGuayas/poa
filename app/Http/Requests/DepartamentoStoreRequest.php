<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class DepartamentoStoreRequest extends Request
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
            'area' => 'required',
            'departamento' => 'required'
        ];
    }
    public function messages()
    {
        return [
            'area.required' => 'El área es requerida',
            'departamento.required' => 'El departamento es requerido',
        ];
    }
}
