<?php

namespace App\Http\Controllers;

use App\Models\Catalogo;
use App\Models\Cliente;
use App\Models\Produto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CatalogoController extends Controller
{

    public function index()
    {
        $sortColumn = request()->get('sort', 'id');
        $sortDirection = request()->get('direction', 'asc');
        
        $allowedColumns = ['id', 'created_at'];
        if (!in_array($sortColumn, $allowedColumns)) {
            $sortColumn = 'id';
        }
        
        $catalogos = Catalogo::with('cliente')
            ->when(request()->search != '', function($query){
                $query->whereHas('cliente', function($q){
                    $q->where('nome','like','%'.request()->search.'%');
                });
            })
            ->orderBy($sortColumn, $sortDirection)
            ->paginate(request()->paginacao ?? 10)
            ->appends(request()->except('page'));

        return view("catalogo.index", compact('catalogos'));
    }


    public function create()
    {
        $clientes = Cliente::select(['id', 'nome'])->get();
        return view("catalogo.form", compact("clientes"));
    }


    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'cliente_id' => 'required',
            ], [
                'cliente_id.required' => 'Necessário informar cliente'
            ]);

            if ($validator->fails()) {
                $errors = $validator->errors();
                $message = $errors->unique();
                return back()->with('messages', ['error' => [implode('<br> ', $message)]])->withInput($request->all());
            }
            $data = [
                'cliente_id' => $request->cliente_id,
            ];
            $catalogo = Catalogo::create($data);
            return redirect(route('catalogo.edit', $catalogo->id))->with('messages', ['success' => ['Catálogo criado com sucesso!']]);
        } catch (\Exception $e) {
            return back()->with('messages', ['error' => ['Não foi possível cadastrar o catálogo!']])->withInput($request->all());
        }
    }



    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $catalogo = Catalogo::find($id);
        
        $clientes = Cliente::select(['id', 'nome'])->get();
        $search = request()->query('search');
        $sortColumn = request()->get('sort', 'id');
        $sortDirection = request()->get('direction', 'asc');
        
        $allowedColumns = ['id', 'modelo', 'codigo', 'ncm', 'descricao', 'created_at'];
        if (!in_array($sortColumn, $allowedColumns)) {
            $sortColumn = 'id';
        }
        
        $produtos = Produto::where('catalogo_id', $id)
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('modelo', 'ILIKE', "%{$search}%")
                        ->orWhere('ncm', 'ILIKE', "%{$search}%")
                        ->orWhere('codigo', 'ILIKE', "%{$search}%")
                        ->orWhere('descricao', 'ILIKE', "%{$search}%");
                });
            })
            ->orderBy($sortColumn, $sortDirection)
            ->paginate(10)
            ->appends(request()->except('page'));
            
        return view("catalogo.form", compact('id', "clientes", 'catalogo', 'produtos'));
    }


    public function update(Request $request, $id)
    {
    }

    public function destroy($id)
    {
        try {
            Produto::where('catalogo_id', $id)->delete();
            Catalogo::find($id)->delete();
            return redirect(route('catalogo.index',))->with('messages', ['success' => ['Catálogo excluído com sucesso!']]);
        } catch (\Exception $e) {
            return back()->with('messages', ['error' => ['Não foi possível excluir o catálogo!']]);
        }
    }
}
