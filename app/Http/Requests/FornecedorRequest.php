<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FornecedorRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Regras de validação
     */
    public function rules(): array
    {
        $fornecedorId = $this->route('fornecedor'); // ID na rota, usado para ignorar o atual no update

        return [
            'nome' => ['required'],
            'cnpj' => [
                Rule::unique('fornecedors', 'cnpj')->ignore($fornecedorId)
            ],
            'cotacao_frete_internacional' => ['nullable', 'numeric'],
            'cotacao_seguro_internacional' => ['nullable', 'numeric'],
            'cotacao_acrescimo_frete' => ['nullable', 'numeric'],
        ];
    }

    /**
     * Mensagens de erro
     */
    public function messages(): array
    {
        return [
            'nome.required' => 'O campo Nome é obrigatório.',
            'cnpj.unique' => 'Não é permitido cadastro duplicado de CNPJ.',

            'cotacao_frete_internacional.numeric' => 'O valor do frete internacional deve ser numérico.',
            'cotacao_seguro_internacional.numeric' => 'O valor do seguro internacional deve ser numérico.',
            'cotacao_acrescimo_frete.numeric' => 'O valor do acréscimo do frete deve ser numérico.',
        ];
    }
}
