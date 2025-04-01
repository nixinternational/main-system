<?php

namespace App\Http\Controllers;

use App\Models\BancoNix;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BancoNixController extends Controller
{
    public function index()
    {
        $bancos = BancoNix::when(request()->search != '',function($query){
            $query->where('nome','like','%'.request()->search.'%');
        })->paginate(request()->paginacao ?? 10);;
        return view('bancos.index', compact('bancos'));
    }

    public function create()
    {
        return view('bancos.form');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nome' => 'required|string|max:255',
            'agencia' => 'required|string|max:255',
            'conta_corrente' => 'required|string|max:255',
            'numero_banco' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return back()->with('messages', ['error' => $validator->errors()->all()])
                ->withInput($request->all());
        }

        BancoNix::create($request->except('_token'));
        return redirect(route('banco-nix.index'))
            ->with('messages', ['success' => ['Banco criado com sucesso!']]);
        
    }

    public function show($id)
    {
        $banco = BancoNix::findOrFail($id);
        return view('bancos.show', compact('banco'));
    }

    public function edit($id)
    {
        $banco = BancoNix::findOrFail($id);
        return view('bancos.form', compact('banco'));
    }

    public function update(Request $request, $id)
    {
        $banco = BancoNix::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'nome' => 'required|string|max:255',
            'agencia' => 'required|string|max:255',
            'conta_corrente' => 'required|string|max:255',
            'numero_banco' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return back()->with('messages', ['error' => $validator->errors()->all()])
                ->withInput($request->all());
        }

        $banco->update($request->except('_token'));
        return redirect(route('banco-nix.index'))
        ->with('messages', ['success' => ['Banco atualizado com sucesso!']]);
    
    }

    public function destroy($id)
    {
        $banco = BancoNix::findOrFail($id);
        $banco->delete();
        return redirect(route('banco-nix.index'))->with('messages', ['success' => ['Banco excluído com sucesso!']]);
    
    }
}
