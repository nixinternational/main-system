<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClienteRequest;
use App\Http\Requests\RelatorioCliente;
use App\Models\Cliente;
use App\Models\ClienteAduana;
use App\Models\ClienteEmail;
use App\Models\ClienteResponsavelProcesso;
use App\Models\Pedido;
use Illuminate\Support\Facades\Validator;
use App\Repositories\ClienteRepository;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClienteController extends Controller
{
    private ClienteRepository $clienteRepository;

    public function __construct(ClienteRepository $clienteRepository)
    {
        $this->clienteRepository = $clienteRepository;
    }
    public function index()
    {
        $clientes = $this->clienteRepository->getAll();
        return view('cliente.index', compact('clientes'));
    }

    public function create()
    {
        return view('cliente.form');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Req uest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'cnpj' => 'required',
                'nome_responsavel_legal' => 'required',
                'cpf_responsavel_legal' => 'required',
            ], [
                'name.required' => 'O campo Nome da empresa é obrigatório!',
                'cnpj.required' => 'O campo Cnpj da empresa é obrigatório!',
                'nome_responsavel_legal.required' => 'O campo Nome - responsável legal é obrigatório!',
                'cpf_responsavel_legal.required' => 'O campo CPF - responsável legal é obrigatório!',
            ]);

            if ($validator->fails()) {
                $errors = $validator->errors();
                $message = $errors->unique();

                return back()->with('messages', ['error' => [implode('<br> ', $message)]])->withInput($request->all());
            }
            $clientData = [
                'nome' => $request->name,
                'cnpj' => $request->cnpj,
                'logradouro' => $request->logradouro,
                'numero' => $request->numero,
                'cep' => $request->cep,
                'complemento' => $request->complemento,
                'bairro' => $request->bairro,
                'cidade' => $request->cidade,
                'estado' => $request->estado,
                'nome_responsavel_legal' => $request->nome_responsavel_legal,
                'cpf_responsavel_legal' => $request->cpf_responsavel_legal,
                'data_vencimento_procuracao' => Carbon::parse($request->data_vencimento_procuracao),
            ];
            $newCliente = Cliente::create($clientData);
            return redirect(route( 'cliente.edit',  $newCliente->id))->with('messages', ['success' => ['Cliente criado com sucesso!']]);
        } catch (\Exception $e) {
            return back()->with('messages', ['error' => ['Não foi possível cadastrar o cliente!']])->withInput($request->all());
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
            $cliente = Cliente::findOrFail($id);
            return view('cliente.form', compact('cliente'));
        } catch (\Exception $e) {
            return back()->with('messages', ['error' => ['Não foi possícel editar a cliente!']]);
        }
    }
    public function clientsByName()
    {
        try {
            $name = request()->query('nome');
            if ($name == '') {

                return response()->json(['success' => true, 'data' => []], 200);
            }
            $resultados = Cliente::where(DB::raw('LOWER(name)'), 'LIKE', '%' . strtolower($name) . '%')
                ->where('tipo_cliente', '!=', null)
                ->where('logradouro', '!=', null)
                ->where('cidade', '!=', null)
                ->select(['name', 'id', 'tipo_cliente'])->get();
            return response()->json(['success' => true, 'data' => $resultados], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => true, 'data' => null, 'message' => 'Erro ao processar requisição. Tente novamente mais tarde.' . $e->getMessage()], 400);
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

            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'cnpj' => 'required',
                'nome_responsavel_legal' => 'required',
                'cpf_responsavel_legal' => 'required',
            ], [
                'name.required' => 'O campo Nome da empresa é obrigatório!',
                'cnpj.required' => 'O campo Cnpj da empresa é obrigatório!',
                'nome_responsavel_legal.required' => 'O campo Nome - responsável legal é obrigatório!',
                'cpf_responsavel_legal.required' => 'O campo CPF - responsável legal é obrigatório!',
            ]);

            if ($validator->fails()) {
                $errors = $validator->errors();
                $message = $errors->unique();

                return back()->with('messages', ['error' => [implode('<br> ', $message)]])->withInput($request->all());
            }
            $cliente = Cliente::findOrFail($id);
            $clientData = [
                'nome' => $request->name,
                'cnpj' => $request->cnpj,
                'logradouro' => $request->logradouro,
                'numero' => $request->numero,
                'cep' => $request->cep,
                'complemento' => $request->complemento,
                'bairro' => $request->bairro,
                'cidade' => $request->cidade,
                'estado' => $request->estado,
                'nome_responsavel_legal' => $request->nome_responsavel_legal,
                'cpf_responsavel_legal' => $request->cpf_responsavel_legal,
                'data_vencimento_procuracao' => $request->data_vencimento_procuracao != null ? Carbon::parse($request->data_vencimento_procuracao) : null,
            ];
            $cliente->update($clientData);
            return redirect(route( 'cliente.edit', $id))->with('messages', ['success' => ['Cliente atualizado com sucesso!']]);

        } catch (\Exception $e) {
            return redirect(route( 'cliente.edit', $id))->with('messages', ['error' => ['Não foi possível atualizar o cliente!!']])->withInput($request->all());
        }
    }

    /**
     *
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            Cliente::findOrFail($id)->delete();
            return redirect(to: route('cliente.index'))->with('messages', ['success' => ['Cliente desativado com sucesso!']]);
        } catch (\Exception $e) {
            return back()->with('messages', ['error' => ['Não foi possícel editar a cliente!']]);
        }
    }

    public function ativar(int $id)
    {
        try {
            Cliente::withTrashed()->where('id', $id)->update(['deleted_at' => null]);
            return back()->with('messages', ['success' => ['Cliente ativado com sucesso!']]);
        } catch (\Exception $e) {
            return back()->with('messages', ['error' => ['Não foi possível ativar a categoria!' . $e->getMessage()]]);
        }
    }

    public function relatorioCliente(RelatorioCliente $request)
    {
        try {
            $datas = explode(' - ', $request->intervalo);

            $inicio = Carbon::createFromFormat('d/m/Y', $datas[0])->startOfDay();

            $fim = Carbon::createFromFormat('d/m/Y', $datas[1])->endOfDay();
            $dados = [];
            foreach ($request->cliente as $cliente) {
                $dados[$cliente]['cliente'] = Cliente::findOrFail($cliente);
                $pedidos = Pedido::where('cliente_id', $cliente)
                    ->whereBetween('dt_previsao', [$inicio, $fim])
                    ->when($request->status != '-1', function ($query) use ($request) {
                        $query->where('status', $request->status);
                    })
                    ->with(['produtos'])->orderBy('dt_previsao', 'ASC')->get();
                $dados[$cliente]['pedidos'] = $pedidos;

                $dados[$cliente]['produtos_total'] =
                    DB::table(DB::table('pedido_produtos', 'pp')
                        ->leftJoin('produtos as p', 'pp.produto_id', '=', 'p.id')
                        ->whereIn(
                            'pp.pedido_id',
                            DB::table('pedidos as pe')
                                ->where('cliente_id', '=', $cliente)
                                ->whereBetween('dt_previsao', [$inicio, $fim])
                                ->pluck('id')
                        )->selectRaw('p.id as id,
                    p.nome as nome,
                    pp.preco * pp.quantidade as preco_total,
                    pp.quantidade as quantidade'), 'pro')
                        ->selectRaw('pro.nome as nome,
                    sum(pro.preco_total	) as preco_total,
                    sum(pro.quantidade) as quantidade_total')
                        ->groupBy('nome')
                        ->orderBy('nome')
                        ->get();
            }

            $pdf = Pdf::loadView('relatorios.pdf.clientes', [
                'dados' => $dados,
                'inicio' => $inicio,
                'fim' => $fim
            ]);
            return $pdf->download("Relatório.pdf");
        } catch (\Exception $e) {
            return response()->json(['success' => true, 'data' => null, 'message' => 'Erro ao processar requisição. Tente novamente mais tarde.' . $e->getMessage()], 400);
        }
    }
    public function relatorioClienteIndex()
    {
        try {
            return view('relatorios.cliente');
        } catch (\Exception $e) {
            return back()->with('messages', ['error' => ['Não foi possível abrir os relatórios!' . $e->getMessage()]]);
        }
    }


    public function updateClientEmail(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'emails' => 'required|min:1',
        ], [
            'emails.min' => 'É necessário informar pelo menos 1 email',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $message = $errors->unique();

            return back()->with('messages', ['error' => [implode('<br> ', $message)]])->withInput($request->all());
        }
        // Buscar todos os emails já cadastrados para o cliente
        $emailsExistentes = ClienteEmail::where('cliente_id', $id)
            ->pluck('email')
            ->toArray();

        // Identificar emails que devem ser adicionados
        $novosEmails = array_diff($request->emails, $emailsExistentes);

        // Identificar emails que foram removidos e devem ser deletados
        $emailsRemovidos = array_diff($emailsExistentes, $request->emails);
        $novosEmails = array_filter($novosEmails);
        // Adicionar novos emails
        foreach ($novosEmails as $email) {
            ClienteEmail::create([
                'cliente_id' => $id,
                'email' => $email
            ]);
        }

        // Remover emails que não estão mais no input
        ClienteEmail::where('cliente_id', $id)
            ->whereIn('email', $emailsRemovidos)
            ->delete();
        return redirect(route('cliente.edit', $id))->with('messages', ['success' => ['Emails atualizados com sucesso!']]);

    }
    public function updateClientAduanas(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'aduanas' => 'required|array|min:1',
        ], [
            'aduanas.min' => 'É necessário informar pelo menos 1 aduana',
        ]);

        if ($validator->fails()) {
            return back()->with('messages', ['error' => [$validator->errors()->first('aduanas')]])
                ->withInput($request->all());
        }

        $aduanasExistentes = ClienteAduana::where('cliente_id', $id)
            ->pluck('nome')
            ->toArray();

        $novasAduanas = array_diff($request->aduanas, $aduanasExistentes);

        $aduanasRemovidas = array_diff($aduanasExistentes, $request->aduanas);
        $novasAduanas = array_filter($novasAduanas);
        foreach ($novasAduanas as $aduana) {
            ClienteAduana::create([
                'cliente_id' => $id,
                'nome' => $aduana
            ]);
        }

        ClienteAduana::where('cliente_id', $id)
            ->whereIn('nome', $aduanasRemovidas)
            ->delete();

        return redirect(route('cliente.edit', $id))
            ->with('messages', ['success' => ['Aduanas atualizadas com sucesso!']]);
    }



    public function updateClientResponsaveis(Request $request, $id)
    {
        // Validação dos dados
        $validator = Validator::make($request->all(), [
            'nomes' => 'required|array|min:1',
            'nomes.*' => 'required|string|max:255',
            'telefones' => 'required|array|min:1',
            'telefones.*' => 'required|string|max:20',
        ], [
            'nomes.min' => 'É necessário informar pelo menos 1 nome',
            'telefones.min' => 'É necessário informar pelo menos 1 telefone',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput($request->all());
        }

        $responsaveisExistentes = ClienteResponsavelProcesso::where('cliente_id', $id)
            ->get(['id', 'nome', 'telefone'])
            ->keyBy('nome')
            ->toArray();

        $responsaveisEnviados = [];
        foreach ($request->nomes as $index => $nome) {
            $responsaveisEnviados[$nome] = [
                'telefone' => $request->telefones[$index] ?? null,
            ];
        }

        foreach ($responsaveisEnviados as $nome => $dados) {
            if (isset($responsaveisExistentes[$nome])) {
                if ($responsaveisExistentes[$nome]['telefone'] !== $dados['telefone']) {
                    ClienteResponsavelProcesso::where('id', $responsaveisExistentes[$nome]['id'])
                        ->update(['telefone' => $dados['telefone']]);
                }
            } else {
                ClienteResponsavelProcesso::create([
                    'cliente_id' => $id,
                    'nome' => $nome,
                    'telefone' => $dados['telefone']
                ]);
            }
        }

        $nomesRemovidos = array_diff(array_keys($responsaveisExistentes), array_keys($responsaveisEnviados));

        ClienteResponsavelProcesso::where('cliente_id', $id)
            ->whereIn('nome', $nomesRemovidos)
            ->delete();
        return redirect(route('cliente.edit', $id))->with('messages', ['success' => ['Responsáveis atualizados com sucesso!']]);

    }
    public function updateClientEspecificidades($id, Request $request)
    {

        try {
            $cliente = Cliente::findOrFail($id);
            $clientData = [

                'despachante_siscomex' => $request->exists('despachante_siscomex'),
                'marinha_mercante' => $request->exists('marinha_mercante'),
                'afrmm' => $request->exists('afrmm'),
                'itau_di' => $request->exists('itau_di'),
                'modalidade_radar' => $request->exists('modalidade_radar') ? $request->modalidade_radar : null,
                'beneficio_fiscal' => $request->beneficio_fiscal,
                'observacoes' => $request->observacoes,
            ];
            $cliente->update($clientData);

            return redirect(to: route('cliente.edit', $id))->with('messages', ['success' => ['Informações específicas atualizadas com sucesso!']]);
        } catch (\Exception $e) {
            return redirect(route('cliente.edit', $id))->with('messages', ['success' => ['Informações específicas atualizadas com sucesso!']]);
        }
    }
}
