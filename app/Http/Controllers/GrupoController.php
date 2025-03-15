<?php

namespace App\Http\Controllers;

use App\Models\Grupo;
use App\Models\GrupoPermissao;
use App\Models\Permissao;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GrupoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $grupos = Grupo::whereNot('id',1)->when(request()->search != '', function ($query) {
            $query->where(DB::raw('lower(nome)'), 'like', '%' . strtolower(request()->cliente) . '%');
        })->paginate(request()->paginacao ?? 30);

        return view("grupo.index", compact("grupos"));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $permissaos = Permissao::all();
        return view("grupo.form", compact('permissaos'));
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
            $grupo = Grupo::create([
                'nome' => $request->nome,
                'slug' => Str::slug($request->nome),
            ]);
            if($request->permissao != null) {
                foreach($request->permissao as $permissao_id) {
                    GrupoPermissao::create([
                        'grupo_id'=> $grupo->id,
                        'permissao_id' => $permissao_id
                    ]);
                }

            }
            return redirect(route('grupo.index'))->with('messages', ['success' => ['Grupo criada com sucesso!']]);
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
        $permissaos = Permissao::all();
        $grupo = Grupo::findOrFail($id);
        $permissoesSelecionadas = $grupo->permissaoId->pluck('permissao_id')->toArray();
        return view("grupo.form",compact('grupo','permissaos','permissoesSelecionadas'));

        } catch (\Exception $e) {
            dd($e);
            return back()->with('messages', ['error' => ['Não foi possível editar o grupo!']]);
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
        try {
            $grupo = Grupo::findOrFail($id);
            GrupoPermissao::where('grupo_id',$id)->whereNotIn('permissao_id',$request->permissao)->delete();
            foreach($request->permissao as $permissao_id){
                GrupoPermissao::updateOrCreate(['grupo_id'=>$id,'permissao_id' => $permissao_id]);
            }
            $grupo->update(['nome' => $request->nome]);
            return redirect(route('grupo.index'))->with('messages', ['success' => ['Grupo criado com sucesso!']]);
    
            } catch (\Exception $e) {
                return back()->with('messages', ['error' => ['Não foi possível editar o grupo!']]);
            }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        GrupoPermissao::where('grupo_id',$id)->delete();
        User::where('grupo_id',$id)->update(['grupo_id'=> null]);
        Grupo::findOrFail($id)->delete();
    }
}
