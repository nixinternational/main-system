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
        
        // Mapeamento de colunas permitidas (usar 'name' ao invés de 'nome' pois é o nome da coluna no banco)
        $allowedColumns = ['id', 'name', 'cnpj', 'cidade', 'created_at'];
        if (!in_array($sortColumn, $allowedColumns)) {
            $sortColumn = 'id';
        }
        
        // Mapear 'nome' para 'name' se necessário
        if ($sortColumn === 'nome') {
            $sortColumn = 'name';
        }
        
        return $this->clientes->withTrashed()
            ->when(request()->search != '', function($query){
                $query->where('name','like','%'.request()->search.'%');
            })
            ->orderBy($sortColumn, $sortDirection)
            ->paginate(request()->paginacao ?? 10)
            ->appends(request()->except('page'));
    }
}