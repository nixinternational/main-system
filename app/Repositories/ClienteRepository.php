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
        return $this->clientes->withTrashed()->when(request()->search != '',function($query){
            $query->where('name','like','%'.request()->search.'%');
        })->paginate(request()->paginacao ?? 10);
    }
}