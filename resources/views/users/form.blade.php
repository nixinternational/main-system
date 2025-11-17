@extends('layouts.app')
@section('title', isset($user) ? "Editar Usuário $user->name" : 'Cadastrar Usuário')


@section('content')
    <div class="card shadow-sm">
        <div class="card-header" style="background: var(--theme-gradient-primary);">
            <h3 class="card-title mb-0 text-white">
                <i class="fas fa-user-shield mr-2"></i>
                {{ isset($user) ? 'Editar Usuário' : 'Cadastrar Usuário' }}
            </h3>
        </div>
        <div class="card-body">
            <form enctype="multipart/form-data"
                action="{{ isset($user) ? route('user.update', $user->id) : route('user.store') }}"
                method="POST">
                @csrf
                @if (isset($user))
                    @method('PUT')
                @endif
                <div class="row">
                    <div class="col-md-4 col-sm-12 mb-3">
                        <label for="nome" class="form-label">Nome</label>
                        <input value="{{ old('nome', $user->name ?? '') }}" class="form-control" name="nome"
                            id="nome">
                        @error('nome')
                            <span class="mt-1  text-red p-1 rounded"><small>{{ $message }}</small></span>
                        @enderror
                    </div>
                    <div class="col-md-4 col-sm-12 mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" value="{{ old('email', $user->email ?? '') }}" class="form-control" name="email"
                            id="email">
                        @error('email')
                            <span class="mt-1  text-red p-1 rounded"><small>{{ $message }}</small></span>
                        @enderror
                    </div>
                    <div class="col-md-4 col-sm-12 mb-3">
                        <label for="senha" class="form-label">Senha</label>
                        <input type="password" class="form-control" name="senha"
                            id="senha" placeholder="Deixe em branco para manter a atual">
                        @error('senha')
                            <span class="mt-1  text-red p-1 rounded"><small>{{ $message }}</small></span>
                        @enderror
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-12">
                <label class="form-label font-weight-bold d-flex align-items-center justify-content-between">
                    Permissões do sistema
                    <div class="form-check mb-0">
                        <input class="form-check-input" type="checkbox" id="selectAllPermissions">
                        <label class="form-check-label" for="selectAllPermissions">
                            Selecionar todas
                        </label>
                    </div>
                </label>
                        @error('permissoes')
                            <div class="alert alert-danger py-2 px-3 mb-3">{{ $message }}</div>
                        @enderror
                    @php
                        $permissoesMarcadas = old('permissoes', $permissoesSelecionadas ?? []);
                        $permissaoAdmin = $permissoes->firstWhere('slug', 'admin');
                    @endphp
                    @if ($permissaoAdmin)
                        <div class="d-flex align-items-center justify-content-between border rounded p-3 mb-3">
                            <span class="font-weight-bold">
                                Administrador: liberar acesso para cadastrar usuários e gerenciar permissões.
                            </span>
                            <div class="form-check mb-0">
                                <input class="form-check-input permissao-checkbox especial" type="checkbox"
                                    id="permissao-admin" name="permissoes[]"
                                    value="{{ $permissaoAdmin->id }}"
                                    {{ in_array($permissaoAdmin->id, $permissoesMarcadas) ? 'checked' : '' }}>
                                <label class="form-check-label" for="permissao-admin">
                                    Ativar administrador
                                </label>
                            </div>
                        </div>
                    @endif
                    @php
                        $agrupar = [
                            'Clientes' => [
                                'listar' => 'clientes_listar',
                                'buscar' => 'clientes_buscar',
                                'cadastro' => ['clientes_cadastrar', 'clientes_editar', 'clientes_dados_cadastrais_visualizar', 'clientes_dados_cadastrais_atualizar'],
                                'abas' => [
                                    'clientes_siscomex_visualizar',
                                    'clientes_siscomex_atualizar',
                                    'clientes_responsaveis_visualizar',
                                    'clientes_responsaveis_gerenciar',
                                    'clientes_aduanas_visualizar',
                                    'clientes_aduanas_gerenciar',
                                    'clientes_documentos_visualizar',
                                    'clientes_documentos_upload',
                                    'clientes_documentos_download',
                                    'clientes_documentos_excluir',
                                    'clientes_fornecedores_visualizar',
                                    'clientes_fornecedores_gerenciar',
                                    'clientes_fornecedores_inativar',
                                    'clientes_fornecedores_reativar',
                                ],
                                'status' => ['clientes_inativar', 'clientes_reativar'],
                            ],
                            'Processos' => [
                                'listar' => 'processos_listar',
                                'buscar' => 'processos_filtrar',
                                'cadastro' => ['processos_criar', 'processos_editar', 'processos_dados_visualizar', 'processos_dados_atualizar'],
                                'produtos' => ['processos_produtos_visualizar', 'processos_produtos_gerenciar', 'processos_produtos_excluir'],
                                'extras' => ['processos_cotacoes_atualizar', 'processos_esboco_visualizar', 'processos_excluir'],
                            ],
                            'Catálogos' => [
                                'listar' => 'catalogos_listar',
                                'buscar' => 'catalogos_buscar',
                                'cadastro' => ['catalogos_cadastrar', 'catalogos_editar', 'catalogos_dados_visualizar'],
                                'produtos' => [
                                    'catalogos_produtos_listar',
                                    'catalogos_produtos_filtros',
                                    'catalogos_produtos_adicionar',
                                    'catalogos_produtos_salvar',
                                    'catalogos_produtos_editar',
                                    'catalogos_produtos_atualizar',
                                    'catalogos_produtos_excluir',
                                ],
                                'status' => ['catalogos_excluir'],
                            ],
                        ];
                    @endphp
                    <div class="accordion permission-accordion" id="accordionPermissoes">
                        @foreach ($agrupar as $titulo => $grupos)
                            @php
                                $slug = \Illuminate\Support\Str::slug($titulo);
                            @endphp
                            <div class="card mb-2">
                                <div class="card-header p-0" id="heading-{{ $slug }}">
                                    <div class="d-flex align-items-center justify-content-between px-3 py-2">
                                        <button class="btn btn-link btn-accordion text-left flex-grow-1" type="button"
                                            data-toggle="collapse"
                                            data-target="#collapse-{{ $slug }}"
                                            aria-expanded="false"
                                            aria-controls="collapse-{{ $slug }}">
                                            <span class="font-weight-bold text-dark">{{ $titulo }}</span>
                                        </button>
                                        <div class="form-check mb-0 ml-2">
                                            <input class="form-check-input select-module" type="checkbox"
                                                data-target="modulo-{{ $slug }}">
                                            <label class="form-check-label small mb-0">Selecionar módulo</label>
                                        </div>
                                    </div>
                                </div>
                                <div id="collapse-{{ $slug }}" class="collapse"
                                    aria-labelledby="heading-{{ $slug }}"
                                    data-parent="#accordionPermissoes">
                                    <div class="card-body" id="modulo-{{ $slug }}">
                                        <div class="row">
                                            @foreach ($grupos as $subtitulo => $permissoesLista)
                                                <div class="col-md-4 col-sm-6 mb-3">
                                                    <div class="border rounded h-100 p-3">
                                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                                            <span class="font-weight-bold text-small text-uppercase">{{ ucfirst($subtitulo) }}</span>
                                                            <div class="form-check mb-0">
                                                                <input class="form-check-input select-group" type="checkbox"
                                                                    data-target="grupo-{{ \Illuminate\Support\Str::slug($titulo . '-' . $subtitulo) }}">
                                                            </div>
                                                        </div>
                                                        @php
                                                            $permissoesIds = is_array($permissoesLista) ? $permissoesLista : [$permissoesLista];
                                                        @endphp
                                                        @foreach ($permissoes->whereIn('slug', $permissoesIds) as $permissao)
                                                            <div class="custom-control custom-checkbox">
                                                                <input class="custom-control-input permissao-checkbox grupo-{{ \Illuminate\Support\Str::slug($titulo . '-' . $subtitulo) }}"
                                                                    type="checkbox"
                                                                    id="permissao-{{ $permissao->id }}"
                                                                    name="permissoes[]"
                                                                    data-slug="{{ $permissao->slug }}"
                                                                    value="{{ $permissao->id }}"
                                                                    {{ in_array($permissao->id, $permissoesMarcadas) ? 'checked' : '' }}>
                                                                <label class="custom-control-label" for="permissao-{{ $permissao->id }}">
                                                                    {{ $permissao->nome }}
                                                                </label>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-12">
                        <label for="clientes_acesso" class="form-label font-weight-bold">Clientes com acesso permitido</label>
                        <select name="clientes[]" id="clientes_acesso" class="form-control select2" multiple data-placeholder="Selecione os clientes permitidos">
                            @php
                                $clientesMarcados = old('clientes', $clientesSelecionados ?? []);
                            @endphp
                            @foreach ($clientes as $cliente)
                                <option value="{{ $cliente->id }}" {{ in_array($cliente->id, $clientesMarcados) ? 'selected' : '' }}>
                                    {{ $cliente->nome }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted d-block mt-1">Somente os clientes selecionados poderão ter catálogos e processos manipulados por este usuário.</small>
                        @error('clientes')
                            <span class="mt-1 text-red p-1 rounded d-block"><small>{{ $message }}</small></span>
                        @enderror
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>

            </form>
        </div>
    </div>

    <script>
        $('.select2').select2({
            width: '100%'
        });

        $('#selectAllPermissions').on('change', function() {
            const checked = $(this).is(':checked');
            $('.permissao-checkbox').prop('checked', checked);
        });

        $('.select-module').on('change', function() {
            const checked = $(this).is(':checked');
            const target = $(this).data('target');
            $('#' + target).find('.permissao-checkbox').prop('checked', checked);
        });

        $('.select-group').on('change', function() {
            const checked = $(this).is(':checked');
            const target = $(this).data('target');
            $('.' + target).prop('checked', checked);
        });

    </script>
@endsection

@push('styles')
    <style>
        .permission-accordion .card {
            border: 1px solid var(--theme-border);
            border-radius: 8px;
            overflow: hidden;
            background: var(--theme-white);
        }

        .permission-accordion .card-header {
            background: linear-gradient(135deg, var(--theme-gray-light) 0%, var(--theme-white) 100%);
            border-bottom: 1px solid var(--theme-border);
        }

        .permission-accordion .btn-accordion {
            color: var(--theme-text);
            text-decoration: none;
            font-weight: 600;
            letter-spacing: 0.3px;
        }

        .permission-accordion .btn-accordion:focus {
            box-shadow: none;
            outline: none;
        }

        .permission-accordion .btn-accordion::after {
            font-family: "Font Awesome 5 Free";
            font-weight: 900;
            content: "\f107";
            float: right;
            transition: transform 0.2s ease;
        }

        .permission-accordion .btn-accordion[aria-expanded="true"]::after {
            transform: rotate(180deg);
        }

        .permission-accordion .form-check-label {
            font-size: 0.85rem;
            color: var(--theme-text-muted, #6c757d);
        }

        .permission-accordion .card-body {
            background: var(--theme-white);
        }
    </style>
@endpush
