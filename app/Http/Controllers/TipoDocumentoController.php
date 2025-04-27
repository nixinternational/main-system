<?php

namespace App\Http\Controllers;

use App\Models\TipoDocumento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TipoDocumentoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tipoDocumentos = TipoDocumento::withTrashed()->paginate(10);
        return view('tipoDocumento.index',compact('tipoDocumentos'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('tipoDocumento.form',);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'nome' => 'required',
                'descricao' => 'required',
            ], [
                'nome.required' => 'O campo Nome do tipo de documento é obrigatório!',
                'descricao.required' => 'Necessário informar a descrição do produto'
            ]);

            if ($validator->fails()) {
                $errors = $validator->errors();
                $message = $errors->unique();
                return back()->with('messages', ['error' => [implode('<br> ', $message)]])->withInput($request->all());
            }
            $data = [
                'nome' => $request->nome,
                'descricao' => $request->descricao,
            ];
            TipoDocumento::create($data);

            return redirect(route('tipo-documento.index'))->with('messages', ['success' => ['Tipo de documento criado com sucesso!']]);
        } catch (\Exception $e) {
            return back()->with('messages', ['error' => ['Não foi possível cadastrar o tipo de documento!']])->withInput($request->all());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $tipoDocumento = TipoDocumento::findOrFail($id);
        return view('tipoDocumento.form',compact('tipoDocumento'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'nome' => 'required',
                'descricao' => 'required',
            ], [
                'nome.required' => 'O campo Nome do tipo de documento é obrigatório!',
                'descricao.required' => 'Necessário informar a descrição do produto'
            ]);

            if ($validator->fails()) {
                $errors = $validator->errors();
                $message = $errors->unique();
                return back()->with('messages', ['error' => [implode('<br> ', $message)]])->withInput($request->all());
            }
            $data = [
                'nome' => $request->nome,
                'descricao' => $request->descricao,
            ];
            TipoDocumento::findOrFail($id)->update($data);

            return redirect(route('tipo-documento.index'))->with('messages', ['success' => ['Tipo de documento criado com sucesso!']]);
        } catch (\Exception $e) {
            return back()->with('messages', ['error' => ['Não foi possível cadastrar o tipo de documento!']])->withInput($request->all());
        }
    }

    public function destroy(int $id)
    {
        try {
            TipoDocumento::findOrFail($id)->delete();
            return back()->with('messages', ['success' => ['Tipo de documento desativado com sucesso!']]);
        } catch (\Exception $e) {
            return back()->with('messages', ['error' => ['Não foi possível excluír o tipo de documento!']]);
        }
    }

    
    public function ativar(int $documento_id)
    {
        try {
            TipoDocumento::withTrashed()->where('id', $documento_id)->update(['deleted_at' => null]);
            return back()->with('messages', ['success' => ['Tipo de documento ativado com sucesso!']]);
        } catch (\Exception $e) {
            return back()->with('messages', ['error' => ['Não foi possível ativar o tipo de documento!' . $e->getMessage()]]);
        }
    }
}
