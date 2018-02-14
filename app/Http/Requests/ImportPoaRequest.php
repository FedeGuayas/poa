<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class ImportPoaRequest extends Request
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
            'poa_file' => 'required|mimes:xlsx,xls,csv|file|max:1024'
        ];
    }
    public function messages()
    {
        return [
            'poa_file.required' => 'Debe seleccionar un archivo con formato correcto',
            'poa_file.max' => 'El archivo es demasiado grande, no exceda 1 MB',
            'poa_file.file' => 'El archivo debe ser cargado',
            'poa_file.mimes' => 'El archivo debe ser de tipo xls,xlsx, csv ',
        ];
    }
}

