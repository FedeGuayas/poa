<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class ProgramaUpdateRequest extends Request
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
            'cod_programa'=>'required|unique:programas,id,:id',
            'programa'=>'required'
        ];
    }

    public function messages()
    {
        return [
            'cod_programa.required' => 'El código del programa es requerido',
            'cod_programa.unique' => 'El código se encuentra registrado',
            'programa.required' => 'El nombre del programa es requerido',
        ];
    }
}
