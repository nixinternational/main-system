<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClienteRequest;
use App\Http\Requests\RelatorioCliente;
use App\Models\BancoCliente;
use App\Models\BancoNix;
use App\Models\Cliente;
use App\Models\ClienteAduana;
use App\Models\ClienteDocumento;
use App\Models\ClienteEmail;
use App\Models\ClienteResponsavelProcesso;
use App\Models\Pedido;
use App\Models\TipoDocumento;
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
        return view('cliente.form', compact('bancosNix','tipoDocumentos'));
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
                'email_responsavel_legal' => $request->email_responsavel_legal,
                'telefone_responsavel_legal' => $request->telefone_responsavel_legal,
            ];
            $newCliente = Cliente::create($clientData);
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
    public function show($id)
    {
    }


    public function edit($id)
    {
        try {
            $bancosNix = BancoNix::all();
            $bancosCliente = BancoCliente::where('cliente_id',$id)->get();
            $cliente = Cliente::findOrFail($id);
            $tipoDocumentos = TipoDocumento::all();
            return view('cliente.form', compact('cliente', 'bancosNix','bancosCliente','tipoDocumentos'));
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
                'email_responsavel_legal' => $request->email_responsavel_legal,
                'telefone_responsavel_legal' => $request->telefone_responsavel_legal,
            ];
            $cliente->update($clientData);
            return redirect(route('cliente.edit', $id))->with('messages', ['success' => ['Cliente atualizado com sucesso!']]);

        } catch (\Exception $e) {
            return redirect(route('cliente.edit', $id))->with('messages', ['error' => ['Não foi possível atualizar o cliente!!']])->withInput($request->all());
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
            return back()->with('messages', ['error' => $validator->errors()->all()])
                ->withInput($request->all());
        }

        $aduanasExistentes = ClienteAduana::where('cliente_id', $id)
        ->get()
        ->map(function ($aduana) {
            return [
                'urf_despacho' => $aduana->urf_despacho,
                'modalidade' => $aduana->modalidade,
            ];
        });
    
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
    
    return redirect(route('cliente.edit', $id))
        ->with('messages', ['success' => ['Aduanas atualizadas com sucesso!']]);
    

    }



    public function updateClientResponsaveis(Request $request, $id)
    {

        if (count($request->nomes) == 0 && count($request->emails) == 0 && count($request->departamento) == 0 && count($request->telefone) == 0) {


            return back()->with('messages', ['error' => ['Não é possível inserir um registro vazio']])->withInput($request->all());
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

        return redirect(route('cliente.edit', $id))->with('messages', ['success' => ['Responsáveis atualizados com sucesso!']]);

    }
    public function updateClientEspecificidades($id, Request $request)
    {

        try {
            DB::beginTransaction();
            $cliente = Cliente::findOrFail($id);
            $clientData = [
                'credenciamento_radar_inicial' => $request->credenciamento_radar_inicial != null ? Carbon::parse($request->credenciamento_radar_inicial) : null,
                'marinha_mercante_inicial' => $request->marinha_mercante_inicial != null ? Carbon::parse($request->marinha_mercante_inicial) : null,
                'afrmm_bb' =>  $request->afrmm_bb == 'true' ,
                'itau_di' => $request->itau_di == 'true',
                'modalidade_radar' => $request->exists('modalidade_radar') ? $request->modalidade_radar : null,
                'beneficio_fiscal' => $request->beneficio_fiscal,
                'observacoes' => $request->observacoes,
                'debito_impostos' => $request->debito_impostos_nix,
                'data_vencimento_procuracao' => $request->data_vencimento_procuracao != null ? Carbon::parse($request->data_vencimento_procuracao) : null,
                'data_procuracao' => $request->data_procuracao != null ? Carbon::parse($request->data_procuracao) : null,
            ];
            $cliente->update($clientData);

            if($request->bancos && count($request->bancos) > 0){
                foreach ($request->bancos as $row => $banco) {
                    // if ($request->debito_impostos_nix == 'nix') {
                    //     $bancoNix = BancoNix::find($banco);
                    //     BancoCliente::where('cliente_id',operator: $id)->where('banco_nix',false)->delete();
                    //         BancoCliente::updateOrCreate(
                    //         [
                    //             'numero_banco' => $bancoNix->numero_banco,
                    //             'cliente_id' => $id,
                    //             'banco_nix' => true,
                    //         ],
                    //         [
                    //             'nome' => $bancoNix->nome,
                    //             'agencia' => $bancoNix->agencia,
                    //             'conta_corrente' => $bancoNix->conta_corrente,
                    //             ]
                    //         );
                    // } else {
                        BancoCliente::where('cliente_id',$id)->where('banco_nix',true)->delete();
                        if($request->numero_bancos[$row] && $request->agencias[$row] && $request->conta_correntes[$row]){
                            BancoCliente::create(
                            [
                                'cliente_id' => $id,
                                'banco_nix' => false,
                                'numero_banco' => $request->numero_bancos[$row] ?? null,
                                'nome' => $banco, 
                                'agencia' => $request->agencias[$row] ?? null,
                                'conta_corrente' => $request->conta_correntes[$row] ?? null,
                            ]
                        );
                        }
                    // }
                }
            }
            DB::commit();

            return redirect( route('cliente.edit', $id).'?tab=custom-tabs-two-home-tab')->with('messages', ['success' => ['Informações específicas atualizadas com sucesso!']]);
        } catch (\Exception $e) {
            dd($e,$request->all());
            DB::rollBack();
            return redirect(route('cliente.edit', $id))->with('messages', ['error' => ['Não foi possível atualizar o cadastro siscomex!']]);
        }
    }

    public function destroyBancoCliente($id){
        $bancoCliente = BancoCliente::find($id);
        $clienteId = $bancoCliente->cliente_id;
        try{
            $bancoCliente->delete();
            return redirect( route('cliente.edit', $clienteId))->with('messages', ['success' => ['Banco excluido com sucesso!']]);
        }catch(\Exception $e){
            return redirect(route('cliente.edit', $clienteId))->with('messages', ['error' => ['Não foi possível excluir o banco do cliente!']]);
        }
    }

    public function updateClientDocument(Request $request,$id){
     
        try{
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
            return redirect( route('cliente.edit', $id))->with('messages', ['success' => ['Documento Adicionado com sucesso!']]);
        }catch(\Exception $e){
            dd($e);
            return redirect(route('cliente.edit', $id))->with('messages', ['error' => ['Não foi possível adicionar o documento do cliente!']]);
        }
    
    }

    public function deleteDocument($id){
        
        try{
            $documentoExistente = ClienteDocumento::findOrFail($id);
            $cliente_id = $documentoExistente->cliente_id;

            Storage::disk('documentos')->delete($documentoExistente->path_file);
            $documentoExistente->delete();
            return redirect( route('cliente.edit', $cliente_id))->with('messages', ['success' => ['Documento Adicionado com sucesso!']]);
        }catch(\Exception $e){
            return redirect(route('cliente.edit', $id))->with('messages', ['error' => ['Não foi possível adicionar o documento do cliente!']]);
        }
    }

}
