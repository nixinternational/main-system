<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Processo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\Process\Process;

class ProcessoController extends Controller
{
   /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $processos = Processo::when(request()->search != '',function($query){
            // $query->where('name','like','%'.request()->search.'%');
        })->paginate(request()->paginacao ?? 10);;
        return view('processo.index',compact('processos'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {        $clientes = Cliente::select(['id', 'nome'])->get();

        return view('processo.form',compact('clientes'));

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
                'cliente_id' => 'required',
            ], [
                'cliente_id.required' => 'O campo cliente do tipo de documento é obrigatório!',

            ]);

            if ($validator->fails()) {
                $errors = $validator->errors();
                $message = $errors->unique();
                return back()->with('messages', ['error' => [implode('<br> ', $message)]])->withInput($request->all());
            }
            $cliente_id = $request->cliente_id;
            $processo = Processo::create([
                'codigo_interno' => $cliente_id . 'processo',
                'cliente_id' => $cliente_id
            ]);

            return redirect(route('processo.edit',$processo->id))->with('messages', ['success' => ['Processo criado com sucesso!']]);
        } catch (\Exception $e) {
            dd($e);
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
        $processo = Processo::find($id);
                    $clientes = Cliente::select(['id', 'nome'])->get();

        return view('processo.form',compact('processo','clientes'));

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


            return redirect(route('tipo-documento.index'))->with('messages', ['success' => ['Tipo de documento criado com sucesso!']]);
        } catch (\Exception $e) {
            return back()->with('messages', ['error' => ['Não foi possível cadastrar o tipo de documento!']])->withInput($request->all());
        }
    }

    public function destroy(int $id)
    {
        try {

            return back()->with('messages', ['success' => ['Tipo de documento desativado com sucesso!']]);
        } catch (\Exception $e) {
            return back()->with('messages', ['error' => ['Não foi possível excluír o tipo de documento!']]);
        }
    }

    

}
