<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PedidoRequest extends FormRequest
{
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
            'motorista' => 'required',
            'dataHora' => 'required',
            'produto' => 'min:1',
            'quantidade' => 'min:1',
            'cliente' => 'required',
            'dataHora' => 'required',
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
            'motorista.required' => 'Obrigatório',
            'dataHora.required' => 'Obrigatório',
            'status.required' => 'Obrigatório',
            'dataHora.required' => 'Obrigatório',
            'produto.min:1' => 'Obrigatório',
            'quantidade.min:1' => 'Obrigatório',
            'cliente.required' => 'Obrigatório',
        ];
    }
}
