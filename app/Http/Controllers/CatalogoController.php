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
        
        $user = auth()->user();
        $allowedClienteIds = $user?->accessibleClienteIds();

        $catalogos = Catalogo::with('cliente')
            ->when($allowedClienteIds !== null, function ($query) use ($allowedClienteIds) {
                $query->whereIn('cliente_id', $allowedClienteIds);
            })
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
        $clientes = $this->clientesDisponiveis();
        abort_if($clientes->isEmpty(), 403, 'Você não possui clientes liberados para criar catálogo.');
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
            $this->ensureClienteAccess((int) $request->cliente_id);
            $cliente = Cliente::findOrFail($request->cliente_id);
            $data = [
                'cliente_id' => $request->cliente_id,
                'cpf_cnpj' => $cliente->cnpj ?? null,
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
        $catalogo = Catalogo::findOrFail($id);
        $this->ensureClienteAccess($catalogo->cliente_id);
        
        $clientes = $this->clientesDisponiveis();
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
            $catalogo = Catalogo::findOrFail($id);
            $this->ensureClienteAccess($catalogo->cliente_id);
            Produto::where('catalogo_id', $catalogo->id)->delete();
            $catalogo->delete();
            return redirect(route('catalogo.index',))->with('messages', ['success' => ['Catálogo excluído com sucesso!']]);
        } catch (\Exception $e) {
            return back()->with('messages', ['error' => ['Não foi possível excluir o catálogo!']]);
        }
    }

    protected function clientesDisponiveis()
    {
        $user = auth()->user();
        $query = Cliente::select(['id', 'nome'])->orderBy('nome');

        $ids = $user?->accessibleClienteIds();
        if ($ids !== null) {
            $query->whereIn('id', $ids);
        }

        return $query->get();
    }

    protected function ensureClienteAccess(int $clienteId): void
    {
        $user = auth()->user();
        if ($user && !$user->canAccessCliente($clienteId)) {
            abort(403, 'Cliente não autorizado para este usuário.');
        }
    }
}
