<?php

namespace App\Http\Controllers;

use App\Models\Grupo;
use App\Models\GrupoPermissao;
use App\Models\Permissao;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
class PermissaoController extends Controller
{
    public function index()
    {
        $permissoes = Permissao::when(request()->search != '', function ($query) {
            $query->where(DB::raw('lower(nome)'), 'like', '%' . strtolower(request()->cliente) . '%');
        })->paginate(request()->paginacao ?? 30);

        return view("permissao.index", compact("permissoes"));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $grupos = Grupo::all();
        $gruposSelecionados = [];
        return view("permissao.form",compact('grupos','gruposSelecionados'));
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
            $permissao = Permissao::create([
                'nome' => $request->nome,
                'slug' => Str::slug($request->nome,'_'),
            ]);
            if($request->grupo != null) {
                foreach($request->grupo as $grupo) {
                    GrupoPermissao::create([
                        'grupo_id'=> $grupo,
                        'permissao_id' => $permissao->id
                    ]);
                }

            }
            return redirect(route('permissao.index'))->with('messages', ['success' => ['Permissão criada com sucesso!']]);
        } catch (\Exception $e) {
            return back()->with('messages', ['error' => ['Não foi possível criar a permissão!']])->withInput($request->all());;
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
  
   
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try {
            $grupos = Grupo::all();
        $permissao = Permissao::findOrFail($id);
        $gruposSelecionados = $permissao->gruposId->pluck('grupo_id')->toArray() ??[];
        return view("permissao.form",compact('grupos','permissao','gruposSelecionados'));

        } catch (\Exception $e) {
            return back()->with('messages', ['error' => ['Não foi possível editar a permissão!']]);
        }
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
        try {
            GrupoPermissao::where('permissao_id',$id)->delete();
            Permissao::findOrFail($id)->delete();
            return back()->with('messages', ['success' => ['Permissão excluída com sucesso!']]);
        } catch (\Exception $e) {
            return back()->with('messages', ['error' => ['Não foi possível excluir a categoria!']]);
        }
    }
}
