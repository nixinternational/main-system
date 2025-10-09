<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Fornecedor;
use App\Repositories\ClienteRepository;
use App\Repositories\FornecedorRepository;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class FornecedorController extends Controller
{
    private ClienteRepository $clienteRepository;
    private FornecedorRepository $fornecedorRepository;

    public function __construct(FornecedorRepository $fornecedorRepository, ClienteRepository $clienteRepository)
    {
        $this->fornecedorRepository = $fornecedorRepository;
        $this->clienteRepository = $clienteRepository;
    }



    public function index(): View|RedirectResponse
    {

        $clientes = $this->clienteRepository->getAll();
        return view('fornecedor.index', compact('clientes'));
    }

    public function create(): View
    {
        return view('fornecedor.form');
    }

    public function store(Request $request): RedirectResponse
    {
        try {
            if (!empty($request->cnpj)) {
                if (!validar_cnpj($request->cnpj)) {
                    return back()->with('messages', ['error' => ['CNPJ inválido!']])->withInput();
                }
            }

            $this->fornecedorRepository->store($request);

            return back()->with('messages', ['success' => ['Fornecedor criado!']])->withInput();
        } catch (\Exception $e) {

            return back()->with('messages', ['error' => ['Não foi possível criar o fornecedor!']])->withInput();
        }
    }

    public function show(int $id)
    {
        // Se necessário no futuro
    }

    public function edit(int $id): View|RedirectResponse
    {
        try {
            $cliente = Cliente::findOrFail($id);
            return view('fornecedor.form', compact('cliente'));
        } catch (\Exception $e) {
            return back()->with('messages', ['error' => ['Não foi possível encontrar o fornecedor!']]);
        }
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        try {

            if (!empty($request->cnpj)) {
                if (!validar_cnpj($request->cnpj)) {
                    return back()->with('messages', ['error' => ['CNPJ inválido!']])->withInput();
                }
            }
            if (empty($request->nome)) {
                return back()->with('messages', ['error' => ['Nome é obrigatório!']])->withInput();
            }


            $this->fornecedorRepository->update($request, $id);

            return back()->with('messages', ['success' => ['Fornecedor atualizado com sucesso']]);
        } catch (\Exception $e) {
            return back()->with('messages', ['error' => ['Não foi possível atualizar o fornecedor!']])->withInput();
        }
    }

    public function destroy(int $id): RedirectResponse
    {
        try {
            $this->fornecedorRepository->destroy($id);
            return back()->with('messages', ['success' => ['Fornecedor excluído com sucesso!']]);
        } catch (\Exception $e) {
            return back()->with('messages', ['error' => ['Requisição inválida!']]);
        }
    }

    public function ativar(int $id): RedirectResponse
    {
        try {
            $this->fornecedorRepository->ativar($id);
            return back()->with('messages', ['success' => ['Fornecedor ativado com sucesso!']]);
        } catch (\Exception $e) {
            return back()->with('messages', ['error' => ['Não foi possível ativar o fornecedor!']]);
        }
    }
}
