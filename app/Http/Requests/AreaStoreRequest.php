<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class AreaStoreRequest extends Request
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
            'area' => 'required |unique:areas,area'
        ];
    }
    public function messages()
    {
        return [
            'area.required' => 'El nombre del área es requerido',
            'area.unique' => 'Ya el área se encuentra registrada',
        ];
    }
}
