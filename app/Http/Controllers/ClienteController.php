<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClienteRequest;
use App\Http\Requests\RelatorioCliente;
use App\Models\BancoCliente;
use App\Models\BancoNix;
use App\Models\Catalogo;
use App\Models\Cliente;
use App\Models\ClienteAduana;
use App\Models\ClienteDocumento;
use App\Models\ClienteEmail;
use App\Models\ClienteResponsavelProcesso;
use App\Models\Pedido;
use App\Models\TipoDocumento;
use App\Services\Auditoria\ClienteAuditService;
use App\Services\Auditoria\CatalogoAuditService;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
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
        $bancosNix = BancoNix::all();
        $tipoDocumentos = TipoDocumento::all();
        return view('cliente.form', compact('bancosNix', 'tipoDocumentos'));
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
            ], [
                'name.required' => 'O campo Nome da empresa é obrigatório!',
                'cnpj.required' => 'O campo Cnpj da empresa é obrigatório!',
                'nome_responsavel_legal.required' => 'O campo Nome - responsável legal é obrigatório!',
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
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
                'email_responsavel_legal' => $request->email_responsavel_legal,
                'telefone_responsavel_legal' => $request->telefone_responsavel_legal,
                'telefone_fixo_responsavel_legal' => $request->telefone_fixo_responsavel_legal,
                'telefone_celular_responsavel_legal' => $request->telefone_celular_responsavel_legal,
            ];
            $newCliente = Cliente::create($clientData);

            // Criar catálogo automaticamente para o novo cliente
            $catalogo = Catalogo::create([
                'cliente_id' => $newCliente->id,
                'cpf_cnpj' => $newCliente->cnpj,
            ]);

            $clienteAuditService = app(ClienteAuditService::class);
            $catalogoAuditService = app(CatalogoAuditService::class);
            $clienteAuditService->logCreate([
                'auditable_type' => Cliente::class,
                'auditable_id' => $newCliente->id,
                'process_type' => 'cliente',
                'process_id' => $newCliente->id,
                'client_id' => $newCliente->id,
                'context' => 'cliente.create',
            ], $newCliente->getAttributes());

            $catalogoAuditService->logCreate([
                'auditable_type' => Catalogo::class,
                'auditable_id' => $catalogo->id,
                'process_type' => 'catalogo',
                'process_id' => $catalogo->id,
                'client_id' => $newCliente->id,
                'context' => 'catalogo.create.auto',
            ], $catalogo->getAttributes());
            
            return redirect(route('cliente.edit', $newCliente->id))->with('messages', ['success' => ['Cliente criado com sucesso!']]);
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
    public function show($id) {}


    public function edit($id)
    {
        try {
            $bancosNix = BancoNix::all();
            $bancosCliente = BancoCliente::where('cliente_id', $id)->get();
            $cliente = Cliente::findOrFail($id);
            $auditService = app(ClienteAuditService::class);
            $clienteOriginal = $cliente->getAttributes();
            $tipoDocumentos = TipoDocumento::all();
            return view('cliente.form', compact('cliente', 'bancosNix', 'bancosCliente', 'tipoDocumentos'));
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
            $resultados = Cliente::where(DB::raw('LOWER(nome)'), 'LIKE', '%' . strtolower($name) . '%')
                ->where('tipo_cliente', '!=', null)
                ->where('logradouro', '!=', null)
                ->where('cidade', '!=', null)
                ->select(['nome', 'id', 'tipo_cliente'])->get();
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
            ], [
                'name.required' => 'O campo Nome da empresa é obrigatório!',
                'cnpj.required' => 'O campo Cnpj da empresa é obrigatório!',
                'nome_responsavel_legal.required' => 'O campo Nome - responsável legal é obrigatório!',
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }
            $cliente = Cliente::findOrFail($id);
            $auditService = app(ClienteAuditService::class);
            $clienteOriginal = $cliente->getAttributes();
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
                'email_responsavel_legal' => $request->email_responsavel_legal,
                'telefone_responsavel_legal' => $request->telefone_responsavel_legal,
                'telefone_fixo_responsavel_legal' => $request->telefone_fixo_responsavel_legal,
                'telefone_celular_responsavel_legal' => $request->telefone_celular_responsavel_legal,
            ];
            $cliente->update($clientData);
            $cliente->refresh();
            $auditService->logUpdate([
                'auditable_type' => Cliente::class,
                'auditable_id' => $cliente->id,
                'process_type' => 'cliente',
                'process_id' => $cliente->id,
                'client_id' => $cliente->id,
                'context' => 'cliente.update',
            ], $clienteOriginal, $cliente->getAttributes());
            return redirect(route('cliente.edit', $id))->with('messages', ['success' => ['Cliente atualizado com sucesso!']]);
        } catch (\Exception $e) {
            return redirect(to: route('cliente.edit', $id))->with('messages', ['error' => ['Não foi possível atualizar o cliente!!']])->withInput($request->all());
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
            $cliente = Cliente::findOrFail($id);
            $auditService = app(ClienteAuditService::class);
            $clienteSnapshot = $cliente->getAttributes();
            $cliente->delete();
            $auditService->logDelete([
                'auditable_type' => Cliente::class,
                'auditable_id' => $cliente->id,
                'process_type' => 'cliente',
                'process_id' => $cliente->id,
                'client_id' => $cliente->id,
                'context' => 'cliente.delete',
            ], $clienteSnapshot);
            return redirect(to: route('cliente.index'))->with('messages', ['success' => ['Cliente desativado com sucesso!']]);
        } catch (\Exception $e) {
            return back()->with('messages', ['error' => ['Não foi possícel editar a cliente!']]);
        }
    }

    public function ativar(int $id)
    {
        try {
            $cliente = Cliente::withTrashed()->findOrFail($id);
            $auditService = app(ClienteAuditService::class);
            $before = [
                'status' => $cliente->deleted_at ? 'Inativo' : 'Ativo',
            ];
            Cliente::withTrashed()->where('id', $id)->update(['deleted_at' => null]);
            $after = [
                'status' => 'Ativo',
            ];
            $auditService->logUpdate([
                'auditable_type' => Cliente::class,
                'auditable_id' => $cliente->id,
                'process_type' => 'cliente',
                'process_id' => $cliente->id,
                'client_id' => $cliente->id,
                'context' => 'cliente.status',
            ], $before, $after);
            return back()->with('messages', ['success' => ['Cliente ativado com sucesso!']]);
        } catch (\Exception $e) {
            return back()->with('messages', ['error' => ['Não foi possível ativar a categoria!' . $e->getMessage()]]);
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
            return back()->withErrors($validator)->withInput();
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
        $auditService = app(ClienteAuditService::class);
        $cliente = Cliente::findOrFail($id);
        $emailsAtualizados = array_values(array_filter($request->emails ?? []));
        $auditService->logUpdate([
            'auditable_type' => Cliente::class,
            'auditable_id' => $cliente->id,
            'process_type' => 'cliente',
            'process_id' => $cliente->id,
            'client_id' => $cliente->id,
            'context' => 'cliente.emails',
        ], ['emails' => $emailsExistentes], ['emails' => $emailsAtualizados]);
        return redirect(route('cliente.edit', $id))->with('messages', ['success' => ['Emails atualizados com sucesso!']]);
    }
    public function updateClientAduanas(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'modalidades' => 'required|array|min:1',
            'modalidades.*' => 'required|in:aereo,maritima,rodoviaria,multimodal,courier',
            'urf_despacho' => 'required|array|min:1',
            'urf_despacho.*' => 'nullable|string', // Agora pode ser nulo
        ], [
            'modalidades.required' => 'É necessário informar pelo menos uma modalidade.',
            'modalidades.*.required' => 'A modalidade é obrigatória.',
            'modalidades.*.in' => 'Modalidade inválida.',
            'urf_despacho.required' => 'É necessário informar pelo menos um URF Despacho.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $aduanasExistentes = ClienteAduana::where('cliente_id', $id)
            ->get()
            ->map(function ($aduana) {
                return [
                    'urf_despacho' => $aduana->urf_despacho,
                    'modalidade' => $aduana->modalidade,
                ];
            });
        $aduanasAntes = $aduanasExistentes->values()->toArray();

        $novasAduanas = [];
        $aduanasParaRemover = $aduanasExistentes->toArray();

        foreach ($request->urf_despacho as $index => $urf_despacho) {
            $modalidade = $request->modalidades[$index] ?? null;

            if (empty($urf_despacho)) {
                continue;
            }

            $novaEntrada = [
                'cliente_id' => $id,
                'urf_despacho' => $urf_despacho,
                'modalidade' => $modalidade,
            ];

            $existe = $aduanasExistentes->contains(function ($aduana) use ($urf_despacho, $modalidade) {
                return $aduana['urf_despacho'] === $urf_despacho && $aduana['modalidade'] === $modalidade;
            });

            if (!$existe) {
                $novasAduanas[] = $novaEntrada;
            }

            // Remove dos que podem ser excluídos
            $aduanasParaRemover = array_filter($aduanasParaRemover, function ($aduana) use ($urf_despacho, $modalidade) {
                return !($aduana['urf_despacho'] === $urf_despacho && $aduana['modalidade'] === $modalidade);
            });
        }

        // Insere novas aduanas
        if (!empty($novasAduanas)) {
            ClienteAduana::insert($novasAduanas);
        }

        // Remove aduanas que não estão mais presentes
        foreach ($aduanasParaRemover as $aduana) {
            ClienteAduana::where('cliente_id', $id)
                ->where('urf_despacho', $aduana['urf_despacho'])
                ->where('modalidade', $aduana['modalidade'])
                ->delete();
        }

        $aduanasDepois = [];
        foreach ($request->urf_despacho as $index => $urf_despacho) {
            if (empty($urf_despacho)) {
                continue;
            }
            $aduanasDepois[] = [
                'urf_despacho' => $urf_despacho,
                'modalidade' => $request->modalidades[$index] ?? null,
            ];
        }
        usort($aduanasAntes, fn ($a, $b) => ($a['urf_despacho'] . $a['modalidade']) <=> ($b['urf_despacho'] . $b['modalidade']));
        usort($aduanasDepois, fn ($a, $b) => ($a['urf_despacho'] . $a['modalidade']) <=> ($b['urf_despacho'] . $b['modalidade']));

        $auditService = app(ClienteAuditService::class);
        $cliente = Cliente::findOrFail($id);
        $auditService->logUpdate([
            'auditable_type' => Cliente::class,
            'auditable_id' => $cliente->id,
            'process_type' => 'cliente',
            'process_id' => $cliente->id,
            'client_id' => $cliente->id,
            'context' => 'cliente.aduanas',
        ], ['aduanas' => $aduanasAntes], ['aduanas' => $aduanasDepois]);

        return redirect(route('cliente.edit', $id))
            ->with('messages', ['success' => ['Aduanas atualizadas com sucesso!']]);
    }



    public function updateClientResponsaveis(Request $request, $id)
    {
        try {

            if ($request->nomes) {
                if (count($request->nomes) == 0 && count($request->emails) == 0 && count($request->departamento) == 0 && count($request->telefone) == 0) {
                    return back()->with('messages', ['error' => ['Não é possível inserir um registro vazio']])->withInput($request->all());
                }
            }

            // Obtendo os responsáveis existentes no banco
            $responsaveisExistentes = ClienteResponsavelProcesso::where('cliente_id', $id)
                ->get(['id', 'nome', 'telefone', 'email', 'departamento'])
                ->keyBy('nome')
                ->toArray();

            $responsaveisEnviados = [];
            foreach ($request->nomes as $index => $nome) {
                if ($nome) {
                    $responsaveisEnviados[$nome] = [
                        'telefone' => $request->telefones[$index] ?? null,
                        'email' => $request->emails[$index] ?? null,
                        'departamento' => $request->departamentos[$index] ?? null,
                    ];
                }
            }

            // Atualizando ou inserindo responsáveis
            foreach ($responsaveisEnviados as $nome => $dados) {
                if (isset($responsaveisExistentes[$nome])) {
                    $responsavel = $responsaveisExistentes[$nome];

                    // Verifica se houve mudança nos dados
                    if (
                        $responsavel['telefone'] !== $dados['telefone'] ||
                        $responsavel['email'] !== $dados['email'] ||
                        $responsavel['departamento'] !== $dados['departamento']
                    ) {
                        ClienteResponsavelProcesso::where('id', $responsavel['id'])
                            ->update([
                                'telefone' => $dados['telefone'],
                                'email' => $dados['email'],
                                'departamento' => $dados['departamento'],
                            ]);
                    }
                } else {
                    ClienteResponsavelProcesso::create([
                        'cliente_id' => $id,
                        'nome' => $nome,
                        'telefone' => $dados['telefone'],
                        'email' => $dados['email'],
                        'departamento' => $dados['departamento'],
                    ]);
                }
            }

            // Removendo responsáveis que não estão mais na lista enviada
            $nomesRemovidos = array_diff(array_keys($responsaveisExistentes), array_keys($responsaveisEnviados));

            $registrosParaExcluir = ClienteResponsavelProcesso::where('cliente_id', $id)
                ->whereIn('nome', $nomesRemovidos)
                ->get();

            foreach ($registrosParaExcluir as $registro) {
                $registro->delete(); // Ou $registro->forceDelete(); se precisar remover permanentemente
            }

            $responsaveisAntes = array_values(array_map(function ($responsavel) {
                return [
                    'nome' => $responsavel['nome'] ?? null,
                    'telefone' => $responsavel['telefone'] ?? null,
                    'email' => $responsavel['email'] ?? null,
                    'departamento' => $responsavel['departamento'] ?? null,
                ];
            }, $responsaveisExistentes));
            $responsaveisDepois = [];
            foreach ($responsaveisEnviados as $nome => $dados) {
                $responsaveisDepois[] = [
                    'nome' => $nome,
                    'telefone' => $dados['telefone'] ?? null,
                    'email' => $dados['email'] ?? null,
                    'departamento' => $dados['departamento'] ?? null,
                ];
            }
            usort($responsaveisAntes, fn ($a, $b) => ($a['nome'] ?? '') <=> ($b['nome'] ?? ''));
            usort($responsaveisDepois, fn ($a, $b) => ($a['nome'] ?? '') <=> ($b['nome'] ?? ''));

            $auditService = app(ClienteAuditService::class);
            $cliente = Cliente::findOrFail($id);
            $auditService->logUpdate([
                'auditable_type' => Cliente::class,
                'auditable_id' => $cliente->id,
                'process_type' => 'cliente',
                'process_id' => $cliente->id,
                'client_id' => $cliente->id,
                'context' => 'cliente.responsaveis',
            ], ['responsaveis' => $responsaveisAntes], ['responsaveis' => $responsaveisDepois]);

            return redirect(route('cliente.edit', $id))->with('messages', ['success' => ['Responsáveis atualizados com sucesso!']]);
        } catch (\Exception $e) {
            dd($e);
        }
    }
    public function updateClientEspecificidades($id, Request $request)
    {
        try {
            DB::beginTransaction();
            $cliente = Cliente::findOrFail($id);
            $auditService = app(ClienteAuditService::class);
            $especificidadesKeys = [
                'credenciamento_radar_inicial',
                'marinha_mercante_inicial',
                'afrmm_bb',
                'itau_di',
                'modalidade_radar',
                'beneficio_fiscal',
                'observacoes',
                'debito_impostos',
                'data_vencimento_procuracao',
                'data_procuracao',
            ];
            $especificidadesAntes = Arr::only($cliente->getAttributes(), $especificidadesKeys);
            $bancosAntes = BancoCliente::where('cliente_id', $id)
                ->get(['numero_banco', 'nome', 'agencia', 'conta_corrente', 'banco_nix'])
                ->toArray();
            $clientData = [
                'credenciamento_radar_inicial' => $request->credenciamento_radar_inicial != null ? Carbon::parse($request->credenciamento_radar_inicial) : null,
                'marinha_mercante_inicial' => $request->marinha_mercante_inicial != null ? Carbon::parse($request->marinha_mercante_inicial) : null,
                'afrmm_bb' =>  $request->afrmm_bb == 'true',
                'itau_di' => $request->itau_di == 'true',
                'modalidade_radar' => $request->exists('modalidade_radar') ? $request->modalidade_radar : null,
                'beneficio_fiscal' => $request->beneficio_fiscal,
                'observacoes' => $request->observacoes,
                'debito_impostos' => $request->debito_impostos_nix,
                'data_vencimento_procuracao' => $request->data_vencimento_procuracao != null ? Carbon::parse($request->data_vencimento_procuracao) : null,
                'data_procuracao' => $request->data_procuracao != null ? Carbon::parse($request->data_procuracao) : null,
            ];
            $cliente->update($clientData);

            // Processar remoção de bancos marcados para exclusão
            if ($request->has('bancos_remover') && $request->has('bancos_ids')) {
                foreach ($request->bancos_ids as $index => $bancoId) {
                    if (!empty($bancoId) && isset($request->bancos_remover[$index]) && $request->bancos_remover[$index] == '1') {
                        BancoCliente::where('id', $bancoId)
                            ->where('cliente_id', $id)
                            ->where('banco_nix', false)
                            ->delete();
                    }
                }
            }

            // Processar bancos (criar novos ou atualizar existentes)
            if ($request->bancos && count($request->bancos) > 0) {
                BancoCliente::where('cliente_id', $id)->where('banco_nix', true)->delete();
                
                foreach ($request->bancos as $row => $banco) {
                    // Verificar se o banco foi marcado para remoção
                    $deveRemover = isset($request->bancos_remover[$row]) && $request->bancos_remover[$row] == '1';
                    
                    if (!$deveRemover && $request->numero_bancos[$row] && $request->agencias[$row] && $request->conta_correntes[$row]) {
                        $bancoId = isset($request->bancos_ids[$row]) ? $request->bancos_ids[$row] : null;
                        
                        if ($bancoId && !empty($bancoId)) {
                            // Atualizar banco existente
                            BancoCliente::where('id', $bancoId)
                                ->where('cliente_id', $id)
                                ->update([
                                    'numero_banco' => $request->numero_bancos[$row] ?? null,
                                    'nome' => $banco,
                                    'agencia' => $request->agencias[$row] ?? null,
                                    'conta_corrente' => $request->conta_correntes[$row] ?? null,
                                ]);
                        } else {
                            // Criar novo banco
                            BancoCliente::create([
                                'cliente_id' => $id,
                                'banco_nix' => false,
                                'numero_banco' => $request->numero_bancos[$row] ?? null,
                                'nome' => $banco,
                                'agencia' => $request->agencias[$row] ?? null,
                                'conta_corrente' => $request->conta_correntes[$row] ?? null,
                            ]);
                        }
                    }
                }
            }
            DB::commit();

            $cliente->refresh();
            $especificidadesDepois = Arr::only($cliente->getAttributes(), $especificidadesKeys);
            $bancosDepois = BancoCliente::where('cliente_id', $id)
                ->get(['numero_banco', 'nome', 'agencia', 'conta_corrente', 'banco_nix'])
                ->toArray();
            $auditService->logUpdate([
                'auditable_type' => Cliente::class,
                'auditable_id' => $cliente->id,
                'process_type' => 'cliente',
                'process_id' => $cliente->id,
                'client_id' => $cliente->id,
                'context' => 'cliente.especificidades',
            ], [
                'especificidades' => $especificidadesAntes,
                'bancos' => $bancosAntes,
            ], [
                'especificidades' => $especificidadesDepois,
                'bancos' => $bancosDepois,
            ]);

            return redirect(route('cliente.edit', $id) . '?tab=custom-tabs-two-home-tab')->with('messages', ['success' => ['Informações específicas atualizadas com sucesso!']]);
        } catch (\Exception $e) {
            dd($e, $request->all());
            DB::rollBack();
            return redirect(route('cliente.edit', $id))->with('messages', ['error' => ['Não foi possível atualizar o cadastro siscomex!']]);
        }
    }

    public function destroyBancoCliente($id)
    {
        $bancoCliente = BancoCliente::find($id);
        $clienteId = $bancoCliente->cliente_id;
        try {
            $auditService = app(ClienteAuditService::class);
            $bancoSnapshot = $bancoCliente?->getAttributes() ?? [];
            $bancoCliente->delete();
            if (!empty($bancoSnapshot)) {
                $auditService->logDelete([
                    'auditable_type' => BancoCliente::class,
                    'auditable_id' => $bancoCliente->id,
                    'process_type' => 'cliente',
                    'process_id' => $clienteId,
                    'client_id' => $clienteId,
                    'context' => 'cliente.banco.delete',
                ], $bancoSnapshot);
            }
            return redirect(route('cliente.edit', $clienteId))->with('messages', ['success' => ['Banco excluido com sucesso!']]);
        } catch (\Exception $e) {
            return redirect(route('cliente.edit', $clienteId))->with('messages', ['error' => ['Não foi possível excluir o banco do cliente!']]);
        }
    }

    public function updateClientDocument(Request $request, $id)
    {

        try {
            $auditService = app(ClienteAuditService::class);
            $cliente = Cliente::findOrFail($id);
            $documentosAntes = ClienteDocumento::where('cliente_id', $id)
                ->get(['tipo_documento', 'path_file', 'url'])
                ->toArray();
            $validator = Validator::make($request->all(), [
                'tipoDocumentos' => 'required|array|min:1',
                'tipoDocumentos.*' => 'required|distinct|not_in:null,""',
                'documentos' => 'required|array',
                // Garante que cada novo tipo de documento tenha um arquivo anexado
                'tipoDocumentos.*' => function ($attribute, $value, $fail) use ($request) {
                    $row = explode('.', $attribute)[1];
                    $isNovo = empty($request->idDocumentos[$row] ?? null);
                    $hasFile = !empty($request->documentos[$row] ?? null);
                    if ($isNovo && !$hasFile) {
                        $fail('É obrigatório anexar um arquivo para cada novo tipo de documento.');
                    }
                },
            ], [
                'tipoDocumentos.required' => 'É necessário informar pelo menos um tipo de documento.',
                'tipoDocumentos.*.required' => 'O tipo de documento não pode ser vazio ou nulo.',
                'tipoDocumentos.*.distinct' => 'Os tipos de documento devem ser distintos.',
                'tipoDocumentos.*.not_in' => 'O tipo de documento não pode ser nulo ou vazio.',
            ]);

            if ($validator->fails()) {
                return back()->with('messages', ['error' => $validator->errors()->all()])
                    ->withInput($request->all());
            }

            foreach ($request->tipoDocumentos as $row => $tipoDocumento) {
                $arquivo = isset($request->documentos[$row]) ? $request->documentos[$row] : null;
                $nomeOriginal = $arquivo ? $arquivo->getClientOriginalName() : null;
                $caminho = $nomeOriginal ? "$id/$nomeOriginal" : null;

                if ($arquivo && $arquivo->isValid()) {
                    Storage::disk('documentos')->putFileAs('', $arquivo, $caminho);
                    $url = Storage::disk('documentos')->url($caminho);
                } else {
                    $url = null;
                    $caminho = null;
                }

                $documentoExistente = ClienteDocumento::find($request->idDocumentos[$row] ?? null);

                if ($documentoExistente) {
                    $documentoExistente->update([
                        'tipo_documento' => $tipoDocumento,
                        'path_file' => $caminho ?? $documentoExistente->path_file,
                        'url' => $url ?? $documentoExistente->url,
                    ]);
                } elseif ($caminho && $url) {
                    ClienteDocumento::create([
                        'cliente_id' => $id,
                        'tipo_documento' => $tipoDocumento,
                        'path_file' => $caminho,
                        'url' => $url,
                    ]);
                }
            }
            $documentosDepois = ClienteDocumento::where('cliente_id', $id)
                ->get(['tipo_documento', 'path_file', 'url'])
                ->toArray();
            $auditService->logUpdate([
                'auditable_type' => Cliente::class,
                'auditable_id' => $cliente->id,
                'process_type' => 'cliente',
                'process_id' => $cliente->id,
                'client_id' => $cliente->id,
                'context' => 'cliente.documentos',
            ], ['documentos' => $documentosAntes], ['documentos' => $documentosDepois]);
            return redirect(route('cliente.edit', $id))->with('messages', ['success' => ['Documento Adicionado com sucesso!']]);
        } catch (\Exception $e) {
            dd($e);
            return redirect(route('cliente.edit', $id))->with('messages', ['error' => ['Não foi possível adicionar o documento do cliente!']]);
        }
    }

    public function deleteDocument($id)
    {

        try {
            $documentoExistente = ClienteDocumento::findOrFail($id);
            $cliente_id = $documentoExistente->cliente_id;

            Storage::disk('documentos')->delete($documentoExistente->path_file);
            $auditService = app(ClienteAuditService::class);
            $documentoSnapshot = $documentoExistente->getAttributes();
            $documentoExistente->delete();
            $auditService->logDelete([
                'auditable_type' => ClienteDocumento::class,
                'auditable_id' => $documentoExistente->id,
                'process_type' => 'cliente',
                'process_id' => $cliente_id,
                'client_id' => $cliente_id,
                'context' => 'cliente.documento.delete',
            ], $documentoSnapshot);
            return redirect(route('cliente.edit', $cliente_id))->with('messages', ['success' => ['Documento excluído com sucesso!']]);
        } catch (\Exception $e) {
            return redirect(route('cliente.edit', $id))->with('messages', ['error' => ['Não foi possível excluir o documento do cliente!']]);
        }
    }
}
