@extends('layouts.app')
@section('title', isset($cliente) ? 'Processos - ' . $cliente->nome : 'Cadastrar Processo')

@section('content')
    <div class="card shadow-sm">
        <div class="card-header" style="background: var(--theme-gradient-primary);">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="card-title mb-0 text-white">
                    <i class="fas fa-file-alt me-2"></i>Processos - {{ $cliente->nome ?? 'Cliente' }}
                </h3>
                <button type="button" class="btn btn-light btn-sm" id="btnCriarProcesso" data-cliente-id="{{ $cliente->id }}">
                    <i class="fas fa-plus me-1"></i>Criar Processo
                </button>
            </div>
        </div>
        <div class="card-body">
            @if (!$processos->isEmpty())
                <div class="table-responsive">
                    <table id="clienteTable" class="table table-striped table-hover mb-0">
                        <thead style="background: var(--theme-gradient-primary);">
                            <tr>
                                <th>{!! sortable('codigo_interno', 'Processo', 'processo-cliente') !!}</th>
                                <th>{!! sortable('descricao', 'Descrição', 'processo-cliente') !!}</th>
                                <th>Tipo</th>
                                <th>{!! sortable('canal', 'Canal', 'processo-cliente') !!}</th>
                                <th>{!! sortable('status', 'Status', 'processo-cliente') !!}</th>
                                <th class="text-center text-white">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($processos as $processo)
                                <tr>
                                    <td>{{ $processo->codigo_interno ?? '-' }}</td>
                                    <td>{{ $processo->descricao ?? 'Sem Descrição' }}</td>
                                    <td>
                                        @php
                                            $tiposMap = [
                                                'maritimo' => 'Marítimo',
                                                'aereo' => 'Aéreo',
                                                'rodoviario' => 'Rodoviário',
                                            ];
                                            $tipoTexto = $tiposMap[$processo->tipo_processo ?? 'maritimo'] ?? 'Marítimo';
                                        @endphp
                                        <span class="badge badge-info">{{ $tipoTexto }}</span>
                                    </td>
                                    <td>
                                        @php
                                            $cores = [
                                                'vermelho' => 'bg-danger',
                                                'verde' => 'bg-success',
                                                'amarelo' => 'bg-warning',
                                            ];
                                            $corClasse = $cores[$processo->canal] ?? 'bg-secondary';
                                        @endphp
                                        <span class="d-inline-block rounded-circle {{ $corClasse }}"
                                            style="width: 30px; height: 30px;"></span>
                                    </td>
                                    <td>
                                        @php
                                            $statusMap = [
                                                'andamento' => 'Em Andamento',
                                                'finalizado' => 'Finalizado',
                                                'prestacao_contas' => 'Prestação de Contas',
                                            ];
                                            $statusTexto = $statusMap[$processo->status] ?? ucfirst(str_replace('_', ' ', $processo->status));
                                        @endphp
                                        {{ $statusTexto }}
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-center" style="gap: 8px;">
                                            <a href="{{ route('processo.edit', $processo->id) }}" 
                                                class="btn btn-sm btn-warning" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form method="POST" class="form-delete-processo d-inline"
                                                action="{{ route('processo.destroy', $processo->id) }}"
                                                enctype="multipart/form-data">
                                                @method('DELETE')
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-danger" title="Excluir">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $processos->appends([])->links() }}
                </div>
            @else
                <x-not-found />
            @endif
        </div>
    </div>


@endsection

@push('styles')
    <style>
        .swal2-popup-custom {
            overflow: visible !important;
            max-height: none !important;
        }
        .swal2-html-container-custom {
            overflow: visible !important;
            max-height: none !important;
            padding: 0 !important;
        }
        .swal2-popup #tipoProcesso {
            overflow: visible !important;
            max-height: none !important;
            height: auto !important;
            display: block !important;
        }
        .swal2-popup #tipoProcesso option {
            padding: 8px;
            background-color: #fff;
        }
        .swal2-popup #tipoProcesso:focus {
            border-color: #b7aa09;
            box-shadow: 0 0 0 0.2rem rgba(183, 170, 9, 0.25);
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            $('.form-delete-processo').on('submit', function(e) {
                e.preventDefault();
                

                Swal.fire({
                    title: 'Tem certeza?',
                    text: "Você realmente deseja excluir este processo? Esta ação não pode ser desfeita!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Sim, excluir!',
                    cancelButtonText: 'Cancelar',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.submit();
                    }
                });
            });

            // Handler para criar processo com seleção de tipo
            $('#btnCriarProcesso').on('click', function() {
                const clienteId = $(this).data('cliente-id');
                
                Swal.fire({
                    title: 'Selecione o Tipo de Processo',
                    html: `
                        <div style="text-align: left; margin-top: 15px;">
                            <select id="tipoProcesso" style="width: 100%; padding: 12px; margin-top: 10px; border: 2px solid #b7aa09; border-radius: 6px; font-size: 16px; background-color: #fff; cursor: pointer; outline: none;">
                                <option value="">-- Selecione uma opção --</option>
                                <option value="maritimo">Marítimo</option>
                                <option value="aereo">Aéreo</option>
                                <option value="rodoviario">Rodoviário</option>
                            </select>
                        </div>
                    `,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Criar Processo',
                    cancelButtonText: 'Cancelar',
                    confirmButtonColor: '#b7aa09',
                    cancelButtonColor: '#6c757d',
                    width: '500px',
                    customClass: {
                        popup: 'swal2-popup-custom',
                        htmlContainer: 'swal2-html-container-custom'
                    },
                    didOpen: () => {
                        // Garantir que o select não tenha scroll e tenha comportamento correto
                        const select = document.getElementById('tipoProcesso');
                        if (select) {
                            select.style.overflow = 'visible';
                            select.style.maxHeight = 'none';
                            select.style.height = 'auto';
                            // Remover qualquer atributo que possa causar scroll
                            select.removeAttribute('size');
                            select.setAttribute('size', '1');
                        }
                        // Garantir que o container do SWAL não tenha altura limitada
                        const popup = document.querySelector('.swal2-popup');
                        if (popup) {
                            popup.style.overflow = 'visible';
                            popup.style.maxHeight = 'none';
                        }
                        const htmlContainer = document.querySelector('.swal2-html-container');
                        if (htmlContainer) {
                            htmlContainer.style.overflow = 'visible';
                            htmlContainer.style.maxHeight = 'none';
                            htmlContainer.style.height = 'auto';
                        }
                    },
                    preConfirm: () => {
                        const tipoProcesso = document.getElementById('tipoProcesso').value;
                        if (!tipoProcesso) {
                            Swal.showValidationMessage('Por favor, selecione um tipo de processo');
                            return false;
                        }
                        return tipoProcesso;
                    }
                }).then((result) => {
                    if (result.isConfirmed && result.value) {
                        // Redirecionar para criar processo com o tipo selecionado
                        window.location.href = `/processo-criar/${clienteId}?tipo_processo=${result.value}`;
                    }
                });
            });
        });
    </script>
@endpush
