<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Producao;
use App\Models\Produto;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;

class ProducaoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $producaosPendentes = Producao::where('status',false)->with(['produto'])
            ->paginate(request()->paginacao ?? 30);

            $producaosConcluidas = Producao::where('status',true)->with(['produto'])
            ->paginate(request()->paginacao ?? 30);
        return view('producao.index', compact('producaosPendentes','producaosConcluidas'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categorias = Categoria::with(['produtos'])->get();
        return view('producao.form',compact('categorias'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        try{
            DB::beginTransaction();
            $producaos = array_filter($request->producao, function($element){
                return $element['quantidade'] != null  && $element['data_inicio'] != null;
            });
            foreach($producaos as $producao){
                Producao::create([
                    'produto_id'=> $producao['produto_id'],
                    'quantidade' => (int) $producao['quantidade'],
                    'dt_inicio' => Carbon::createFromFormat('d/m/Y H:i',$producao['data_inicio']),
                ]);
            }
            DB::commit();
            return redirect(route('producao.index'))->with('messages', ['success' => ['Produção cadastrada com sucesso!']]);
        } catch (Exception $e) {
            dd($e,$producao);
            DB::rollBack();
            return back()->with('messages', ['error' => ['Não foi possível cadastrar a produção!']])->withInput($request->all());
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
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
