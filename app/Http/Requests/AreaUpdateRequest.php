<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class AreaUpdateRequest extends Request
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
            //'area' => 'required | unique:areas,area,:id'// :id excludes the current row ID (editing)
            //'area' => 'required | unique:areas,area,:id'.$this->get('id') idem al de arriba
            'area' => 'required | unique:areas,area'


        ];
    }
    public function messages()
    {
        return [
            'area.required' => 'El nombre del área es requerido',
            'area.unique' => 'El área se encuentra registrada',
        ];
    }
}
