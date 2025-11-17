<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Permissao;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $sortColumn = request()->get('sort', 'id');
        $sortDirection = request()->get('direction', 'asc');
        
        $allowedColumns = ['id', 'name', 'email', 'created_at'];
        if (!in_array($sortColumn, $allowedColumns)) {
            $sortColumn = 'id';
        }
        
        $users = User::whereNotIn('email', User::superAdminEmails())
            ->orderBy($sortColumn, $sortDirection)
            ->paginate(request()->paginacao ?? 10)
            ->appends(request()->except('page'));

        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $permissoes = Permissao::orderBy('nome')->get();
        $clientes = Cliente::orderBy('nome')->select('id', 'nome')->get();
        $permissoesSelecionadas = [];
        $clientesSelecionados = [];

        return view('users.form', compact('permissoes', 'clientes', 'permissoesSelecionadas', 'clientesSelecionados'));
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
            $dados = $request->validate([
                'nome' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:255', 'unique:users,email'],
                'senha' => ['required', 'string', 'min:6'],
                'permissoes' => ['required', 'array', 'min:1'],
                'permissoes.*' => ['exists:permissaos,id'],
                'clientes' => ['nullable', 'array'],
                'clientes.*' => ['exists:clientes,id'],
            ]);

            DB::beginTransaction();
            $user =  User::create([
                'name' => $dados['nome'],
                'email' => $dados['email'],
                'email_verified_at' => Carbon::now(),
                'password' => Hash::make($dados['senha']),
                'grupo_id' => null,
                'active' => true,
            ]);

            $user->syncPermissions($dados['permissoes'] ?? []);
            $user->syncClientes($dados['clientes'] ?? []);
            DB::commit();
            return redirect(route('user.index'))->with('messages', ['success' => ['Usuário criada com sucesso!']]);
        } catch (Exception $e) {
            DB::rollback();
            return back()->with('messages', ['error' => ['Não foi possível criar o usuário!']])->withInput($request->all());;
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {}

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        $permissoes = Permissao::orderBy('nome')->get();
        $clientes = Cliente::orderBy('nome')->select('id', 'nome')->get();
        $permissoesSelecionadas = $user->permissoes()->pluck('permissao_id')->toArray();
        $clientesSelecionados = $user->clientesPermitidos()->pluck('cliente_id')->toArray();

        return view('users.form', compact('user', 'permissoes', 'clientes', 'permissoesSelecionadas', 'clientesSelecionados'));
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
            $user = User::findOrFail($id);
            DB::beginTransaction();
            $dados = $request->validate([
                'nome' => ['required', 'string', 'max:255'],
                'email' => [
                    'required',
                    'email',
                    'max:255',
                    Rule::unique('users', 'email')->ignore($user->id),
                ],
                'senha' => ['nullable', 'string', 'min:6'],
                'permissoes' => ['required', 'array', 'min:1'],
                'permissoes.*' => ['exists:permissaos,id'],
                'clientes' => ['nullable', 'array'],
                'clientes.*' => ['exists:clientes,id'],
            ]);

            $user->update([
                'name' => $dados['nome'],
                'email' => $dados['email'],
                'email_verified_at' => Carbon::now(),
            ]);

            if (!empty($dados['senha'])) {
                $user->update([
                    'password' => Hash::make(trim($dados['senha'])),
                ]);
            }

            $user->syncPermissions($dados['permissoes'] ?? []);
            $user->syncClientes($dados['clientes'] ?? []);

            DB::commit();
            return redirect(route('user.index'))->with('messages', ['success' => ['Usuário atualizado com sucesso!']]);
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('messages', ['error' => ['Não foi possível criar o usuário!']])->withInput($request->all());;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {}

    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);

        if (User::isSuperAdminEmail($user->email)) {
            return back()->with('messages', ['error' => ['Não é possível desativar o administrador principal.']]);
        }

        $user->update([
            'active' => !$user->active,
        ]);

        return back()->with('messages', ['success' => [$user->active ? 'Usuário reativado com sucesso!' : 'Usuário desativado com sucesso!']]);
    }

    public function toggleIpProtection()
    {
        // Nome da chave no cache
        $key = 'ip_protection_enabled';

        // Busca o valor atual; se não existir inicializa com false
        $current = Cache::rememberForever($key, fn() => false);

        // Inverte o valor
        $new = !$current;

        // Atualiza no cache
        Cache::forever($key, $new);

        return redirect('/home')
            ->with('success', $new ? 'Proteção de IP habilitada' : 'Proteção de IP desabilitada');
    }
}
