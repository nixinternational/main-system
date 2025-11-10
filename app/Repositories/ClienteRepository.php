<?php
namespace App\Repositories;

use App\Models\Cliente;

class ClienteRepository{
    private Cliente $clientes;

    public function __construct(Cliente $clientes)
    {
        $this->clientes = $clientes;
    }

    public function getAll(){
        $sortColumn = request()->get('sort', 'id');
        $sortDirection = request()->get('direction', 'asc');
        
        // Mapeamento de colunas permitidas (coluna correta é 'nome')
        $allowedColumns = ['id', 'nome', 'cnpj', 'cidade', 'created_at'];
        if (!in_array($sortColumn, $allowedColumns)) {
            $sortColumn = 'id';
        }
        
        // Mapear 'name' para 'nome' se necessário (compatibilidade)
        if ($sortColumn === 'name') {
            $sortColumn = 'nome';
        }
        
        return $this->clientes->withTrashed()
            ->when(request()->search != '', function($query){
                $query->where('nome','like','%'.request()->search.'%');
            })
            ->orderBy($sortColumn, $sortDirection)
            ->paginate(request()->paginacao ?? 10)
            ->appends(request()->except('page'));
    }
}