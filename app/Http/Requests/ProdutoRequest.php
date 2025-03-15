<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProdutoRequest extends FormRequest
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
            "nome" => "required",
            "unidade" => "required",
            "precoA" => "required",
            "precoB" => "required",
            "precoC" => "required",

            // "responsavel" => "required",
        ];
    }

    public function messages()
    {
        return [
            "nome.required" => "Obrigatório",
            "unidade.required" => "Obrigatório",
            "precoA.required" => "Obrigatório",
            "precoB.required" => "Obrigatório",
            "precoC.required" => "Obrigatório",

            // "responsavel.required" => "O campo responsavel é obrigatório",
        ];
    }
}
