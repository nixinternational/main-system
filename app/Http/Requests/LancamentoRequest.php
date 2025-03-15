<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LancamentoRequest extends FormRequest
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
            'lote_id' => 'required',
            'quantidade' => 'required|gt:0'
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
            'lote_id.required' => 'É necessário informar o código do lote dos produtos a serem retirados',
            'quantidade.required' => 'É necessário informar a quantidade de produtos a serem retirados',

            'quantidade.gt' => 'Deve ser maior que 0',

        ];
    }
}
