<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClienteRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => 'required',
            'cnpj' => 'required',
            'cep' => 'required',
            'logradouro' => 'required',
            'numero' => 'required',
            'bairro' => 'required',
            'cidade' => 'required',
            'tipo_cliente' => 'required',
           
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Obrigatório',
            'cnpj.required' => 'Obrigatório',
            'cep.required' => 'Obrigatório',
            'logradouro.required' => 'Obrigatório',
            'numero.required' => 'Obrigatório',
            'bairro.required' => 'Obrigatório',
            'cidade.required' => 'Obrigatório',
            'tipo_cliente.required' => 'Obrigatório',

        ];
    }
}
